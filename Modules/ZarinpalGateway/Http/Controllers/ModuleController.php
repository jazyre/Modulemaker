<?php

namespace Modules\ZarinpalGateway\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Nwidart\Modules\Facades\Module;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        // In a real app, you'd get modules from the nwidart/laravel-modules package
        $modules = Module::all(); // This facade provides module information

        // For the purpose of this file, we can imagine the view exists
        // return view('zarinpalgateway::index', compact('modules'));

        // Since we can't render a view, we'll return the data as JSON
        return response()->json($modules);
    }

    /**
     * Show the form for editing the specified resource.
     * @param string $module
     * @return Renderable
     */
    public function settings($module)
    {
        $moduleName = ucfirst($module); // e.g., Zarinpalgateway -> ZarinpalGateway

        $settings = DB::table('module_settings')->where('module', $moduleName)->get()->keyBy('key');
        $listeners = DB::table('module_listeners')->where('module', $moduleName)->get();
        $events = DB::table('module_events')->where('module', $moduleName)->get();

        // return view('zarinpalgateway::settings', compact('moduleName', 'settings', 'listeners', 'events'));
        return response()->json(compact('moduleName', 'settings', 'listeners', 'events'));
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
        $settings = $request->except('_token');

        foreach ($settings as $key => $value) {
            DB::table('module_settings')->updateOrInsert(
                ['module' => $moduleName, 'key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        // return back()->with('success', 'Settings updated successfully!');
        return response()->json(['message' => 'Settings updated successfully!']);
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

        return response()->json(['message' => 'Listener added successfully!']);
    }

    /**
     * Remove the specified listener from storage.
     * @param int $listenerId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyListener($module, $listenerId)
    {
        DB::table('module_listeners')->where('id', $listenerId)->delete();
        return response()->json(['message' => 'Listener removed successfully!']);
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

        return response()->json(['message' => 'Event added successfully!']);
    }

    /**
     * Remove the specified event from storage.
     * @param int $eventId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyEvent($module, $eventId)
    {
        DB::table('module_events')->where('id', $eventId)->delete();
        return response()->json(['message' => 'Event removed successfully!']);
    }
}
