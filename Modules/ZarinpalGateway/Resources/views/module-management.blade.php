<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Management</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 80%; margin: 0 auto; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
        .btn { padding: 5px 10px; border: none; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-enable { background-color: #4CAF50; color: white; }
        .btn-disable { background-color: #f44336; color: white; }
        .btn-settings { background-color: #008CBA; color: white; }
        .btn-export { background-color: #f0ad4e; color: white; }
        .import-form { margin-top: 2rem; padding: 1rem; border: 1px solid #ddd; }
    </style>
</head>
<body>
<div class="container">
    <h1>Module Management</h1>

    <table>
        <thead>
            <tr>
                <th>Module Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($modules as $module)
                <tr>
                    <td>{{ $module->getName() }}</td>
                    <td>{{ $module->getDescription() }}</td>
                    <td>
                        @if ($module->isEnabled())
                            <span style="color: green;">Enabled</span>
                        @else
                            <span style="color: red;">Disabled</span>
                        @endif
                    </td>
                    <td>
                        @if ($module->isEnabled())
                            <a href="{{ route('admin.modules.disable', ['module' => $module->getLowerName()]) }}" class="btn btn-disable">Disable</a>
                        @else
                            <a href="{{ route('admin.modules.enable', ['module' => $module->getLowerName()]) }}" class="btn btn-enable">Enable</a>
                        @endif
                        <a href="{{ route('admin.modules.settings', ['module' => $module->getLowerName()]) }}" class="btn btn-settings">Settings</a>
                        <a href="{{ route('admin.modules.export', ['module' => $module->getLowerName()]) }}" class="btn btn-export">Export</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="import-form">
        <h2>Import Module</h2>
        <form action="{{ route('admin.modules.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" name="module_zip" required>
            <button type="submit" class="btn">Import</button>
        </form>
    </div>
</div>
</body>
</html>
