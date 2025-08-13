@extends('layouts.app')

@section('title', 'مدیریت ماژول‌ها')

@section('content')
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-2xl font-bold mb-4">لیست ماژول‌ها</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="py-3 px-6 text-right font-bold uppercase">نام ماژول</th>
                        <th class="py-3 px-6 text-right font-bold uppercase">توضیحات</th>
                        <th class="py-3 px-6 text-center font-bold uppercase">وضعیت</th>
                        <th class="py-3 px-6 text-center font-bold uppercase">عملیات</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach ($modules as $module)
                        <tr class="border-b">
                            <td class="py-3 px-6">{{ $module->getName() }}</td>
                            <td class="py-3 px-6">{{ $module->getDescription() }}</td>
                            <td class="py-3 px-6 text-center">
                                @if ($module->isEnabled())
                                    <span class="bg-green-200 text-green-800 py-1 px-3 rounded-full text-xs">فعال</span>
                                @else
                                    <span class="bg-red-200 text-red-800 py-1 px-3 rounded-full text-xs">غیرفعال</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                @if ($module->isEnabled())
                                    <a href="{{ route('admin.modules.disable', ['module' => $module->getLowerName()]) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-xs">غیرفعال کردن</a>
                                @else
                                    <a href="{{ route('admin.modules.enable', ['module' => $module->getLowerName()]) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-xs">فعال کردن</a>
                                @endif
                                <a href="{{ route('admin.modules.settings', ['module' => $module->getLowerName()]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-xs">تنظیمات</a>
                                <a href="{{ route('admin.modules.export', ['module' => $module->getLowerName()]) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-xs">خروجی</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white shadow rounded-lg p-6 mt-6">
        <h2 class="text-2xl font-bold mb-4">وارد کردن ماژول جدید</h2>
        <form action="{{ route('admin.modules.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div>
                <label for="module_zip" class="block text-sm font-medium text-gray-700">فایل Zip ماژول</label>
                <input type="file" name="module_zip" id="module_zip" required class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
            </div>
            <div class="mt-4">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    وارد کردن
                </button>
            </div>
        </form>
    </div>
@endsection
