@extends('layouts.app')

@section('title', 'تنظیمات ماژول - ' . $moduleName)

@section('content')
<div class="space-y-6">
    <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <div class="md:grid md:grid-cols-3 md:gap-6">
            <div class="md:col-span-1">
                <h3 class="text-lg font-medium leading-6 text-gray-900">تنظیمات عمومی</h3>
                <p class="mt-1 text-sm text-gray-500">
                    تنظیمات اصلی و عمومی ماژول را در اینجا وارد کنید.
                </p>
            </div>
            <div class="mt-5 md:mt-0 md:col-span-2">
                <form action="{{ route('admin.modules.settings.update', ['module' => $moduleName]) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-6 gap-6">
                        <div class="col-span-6 sm:col-span-4">
                            <label for="merchant_id" class="block text-sm font-medium text-gray-700">Merchant ID</label>
                            <input type="text" name="merchant_id" id="merchant_id" value="{{ $settings['merchant_id']->value ?? '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>

                        <div class="col-span-6 sm:col-span-4">
                            <label for="callback_url" class="block text-sm font-medium text-gray-700">Callback URL</label>
                            <input type="text" name="callback_url" id="callback_url" value="{{ $settings['callback_url']->value ?? '' }}" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 mt-4">
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            ذخیره
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">رویدادهای ورودی (شنونده‌ها)</h3>
        <!-- Table for Listeners -->
        <table class="min-w-full bg-white mb-4">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-4 text-right">رویداد</th>
                    <th class="py-2 px-4 text-right">شنونده</th>
                    <th class="py-2 px-4 text-center">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listeners as $listener)
                <tr class="border-b">
                    <td class="py-2 px-4 font-mono text-sm">{{ $listener->event }}</td>
                    <td class="py-2 px-4 font-mono text-sm">{{ $listener->listener }}</td>
                    <td class="py-2 px-4 text-center">
                        <form action="{{ route('admin.modules.listeners.destroy', ['module' => $moduleName, 'listenerId' => $listener->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Form to add new listener -->
        <form action="{{ route('admin.modules.listeners.store', ['module' => $moduleName]) }}" method="POST" class="border-t pt-4">
            @csrf
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="event" class="block text-sm font-medium text-gray-700">کلاس رویداد</label>
                    <input type="text" name="event" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Modules\Shop\Events\OrderCreated" required>
                </div>
                <div class="sm:col-span-3">
                    <label for="listener" class="block text-sm font-medium text-gray-700">کلاس شنونده</label>
                    <input type="text" name="listener" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Modules\{{ $moduleName }}\Listeners\ProcessPayment" required>
                </div>
            </div>
            <div class="pt-5">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">افزودن شنونده</button>
            </div>
        </form>
    </div>

    <div class="bg-white shadow px-4 py-5 sm:rounded-lg sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">رویدادهای خروجی (ارسال‌کننده‌ها)</h3>
        <!-- Table for Events -->
        <table class="min-w-full bg-white mb-4">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-2 px-4 text-right">رویداد</th>
                    <th class="py-2 px-4 text-right">توضیحات</th>
                    <th class="py-2 px-4 text-center">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr class="border-b">
                    <td class="py-2 px-4 font-mono text-sm">{{ $event->event }}</td>
                    <td class="py-2 px-4">{{ $event->description }}</td>
                    <td class="py-2 px-4 text-center">
                        <form action="{{ route('admin.modules.events.destroy', ['module' => $moduleName, 'eventId' => $event->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Form to add new event -->
        <form action="{{ route('admin.modules.events.store', ['module' => $moduleName]) }}" method="POST" class="border-t pt-4">
            @csrf
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="event" class="block text-sm font-medium text-gray-700">کلاس رویداد</label>
                    <input type="text" name="event" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Modules\{{ $moduleName }}\Events\PaymentProcessed" required>
                </div>
                <div class="sm:col-span-3">
                    <label for="description" class="block text-sm font-medium text-gray-700">توضیحات</label>
                    <input type="text" name="description" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                </div>
            </div>
            <div class="pt-5">
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">افزودن رویداد</button>
            </div>
        </form>
    </div>
</div>
@endsection
