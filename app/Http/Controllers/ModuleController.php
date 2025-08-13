<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Modules\ZarinpalGateway\Entities\ZarinpalGateway;
use Illuminate\Support\Facades\Storage;
use Nwidart\Modules\Facades\Module;
use ZipArchive;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $modules = Module::all();
        // The view path needs to be specified correctly. Since this controller
        // is in the ZarinpalGateway module, we can reference its views.
        return view('zarinpalgateway::module-management', compact('modules'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param string $module
     * @return Renderable
     */
    public function settings($module)
    {
        $moduleName = ucfirst($module);

        $settings = DB::table('module_settings')->where('module', $moduleName)->get()->keyBy('key');
        $listeners = DB::table('module_listeners')->where('module', $moduleName)->get();
        $events = DB::table('module_events')->where('module', $moduleName)->get();

        return view('zarinpalgateway::module-settings', compact('moduleName', 'settings', 'listeners', 'events'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $module
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettings(Request $request, $module)
    {
        $moduleName = ucfirst($module);
        $settings = $request->except('_token', '_method');

        foreach ($settings as $key => $value) {
            DB::table('module_settings')->updateOrInsert(
                ['module' => $moduleName, 'key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Settings updated successfully!');
    }

    /**
     * Store a newly created listener in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeListener(Request $request, $module)
    {
        $request->validate([
            'event' => 'required|string',
            'listener' => 'required|string',
        ]);

        DB::table('module_listeners')->insert([
            'module' => ucfirst($module),
            'event' => $request->event,
            'listener' => $request->listener,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Listener added successfully!');
    }

    /**
     * Remove the specified listener from storage.
     * @param int $listenerId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyListener($module, $listenerId)
    {
        DB::table('module_listeners')->where('id', $listenerId)->delete();
        return back()->with('success', 'Listener removed successfully!');
    }

    /**
     * Store a newly created event in storage.
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeEvent(Request $request, $module)
    {
        $request->validate([
            'event' => 'required|string',
            'description' => 'nullable|string',
        ]);

        DB::table('module_events')->insert([
            'module' => ucfirst($module),
            'event' => $request->event,
            'description' => $request->description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Event added successfully!');
    }

    /**
     * Remove the specified event from storage.
     * @param int $eventId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyEvent($module, $eventId)
    {
        DB::table('module_events')->where('id', $eventId)->delete();
        return back()->with('success', 'Event removed successfully!');
    }

    /**
     * Export a module as a zip archive.
     *
     * @param string $module
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export($module)
    {
        $moduleInstance = Module::find($module);
        if (!$moduleInstance) {
            return back()->with('error', 'Module not found!');
        }

        $modulePath = $moduleInstance->getPath();
        $zipFileName = $module . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
            return back()->with('error', 'Could not create zip archive.');
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($modulePath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($modulePath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    /**
     * Import a module from a zip archive.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function import(Request $request)
    {
        $request->validate([
            'module_zip' => 'required|file|mimes:zip',
        ]);

        $file = $request->file('module_zip');
        $tempPath = $file->store('temp');
        $zipPath = storage_path('app/' . $tempPath);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== TRUE) {
            Storage::delete($tempPath);
            return back()->with('error', 'Could not open the zip file.');
        }

        // Basic validation: check for module.json
        if ($zip->locateName('module.json') === false) {
            $zip->close();
            Storage::delete($tempPath);
            return back()->with('error', 'Invalid module archive: module.json not found.');
        }

        $moduleName = json_decode($zip->getFromName('module.json'))->name;
        $modulesPath = config('modules.paths.modules');
        $extractPath = $modulesPath . '/' . $moduleName;

        if (File::exists($extractPath)) {
             $zip->close();
             Storage::delete($tempPath);
             return back()->with('error', 'Module already exists!');
        }

        $zip->extractTo($modulesPath);
        $zip->close();

        Storage::delete($tempPath);

        // Optional: Run migrations or other setup for the new module
        // Artisan::call('module:migrate', ['module' => $moduleName]);

        return back()->with('success', 'Module imported successfully!');
    }

    /**
     * Enable the specified module.
     *
     * @param string $module
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enable($module)
    {
        $moduleInstance = Module::find($module);
        if (!$moduleInstance) {
            return back()->with('error', 'Module not found!');
        }

        $moduleInstance->enable();

        return back()->with('success', 'Module enabled successfully!');
    }

    /**
     * Disable the specified module.
     *
     * @param string $module
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disable($module)
    {
        $moduleInstance = Module::find($module);
        if (!$moduleInstance) {
            return back()->with('error', 'Module not found!');
        }

        $moduleInstance->disable();

        return back()->with('success', 'Module disabled successfully!');
    }

    /**
     * Handle the payment callback from the gateway.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        // This is a simplified callback. In a real app, you'd fetch the order
        // from the database based on a session variable or a query parameter.
        $orderId = $request->query('orderId', 1); // Dummy Order ID
        // $order = Order::findOrFail($orderId);
        $amount = 1000; // You'd get this from the order: $order->amount

        $data = [
            'orderId' => $orderId,
            'amount' => $amount,
            'authority' => $request->query('Authority'),
        ];

        $paymentGateway = new ZarinpalGateway();
        $result = $paymentGateway->verify($data);

        if ($result['status'] === 'success') {
            // Payment was successful. Update order status, etc.
            // Order::where('id', $orderId)->update(['status' => 'paid', 'ref_id' => $result['ref_id']]);
            return redirect('/')->with('success', 'Payment successful! Ref ID: ' . $result['ref_id']);
        } else {
            // Payment failed.
            return redirect('/')->with('error', 'Payment failed: ' . $result['message']);
        }
    }
}
