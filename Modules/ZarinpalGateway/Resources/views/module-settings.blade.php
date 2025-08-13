<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Module Settings - {{ $moduleName }}</title>
    <style>
        body { font-family: sans-serif; }
        .container { width: 80%; margin: 0 auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"] { width: 100%; padding: 8px; border: 1px solid #ddd; }
        .btn { padding: 10px 15px; border: none; cursor: pointer; background-color: #4CAF50; color: white; }
        .btn-danger { background-color: #f44336; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        hr { margin: 2rem 0; }
    </style>
</head>
<body>

    <div class="container">
        <h1>Module Settings - {{ $moduleName }}</h1>

        <h2>General Settings</h2>
        <form action="{{ route('admin.modules.settings.update', ['module' => $moduleName]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="merchant_id">Merchant ID</label>
                <input type="text" id="merchant_id" name="merchant_id" value="{{ $settings['merchant_id']->value ?? '' }}">
            </div>
            <div class="form-group">
                <label for="callback_url">Callback URL</label>
                <input type="text" id="callback_url" name="callback_url" value="{{ $settings['callback_url']->value ?? '' }}">
            </div>
            <button type="submit" class="btn">Save Settings</button>
        </form>

        <hr>

        <h2>Events & Listeners</h2>

        <h3>Input Events (Listeners)</h3>
        <p>Define which events this module should listen to.</p>
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Listener</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($listeners as $listener)
                <tr>
                    <td>{{ $listener->event }}</td>
                    <td>{{ $listener->listener }}</td>
                    <td>
                        <form action="{{ route('admin.modules.listeners.destroy', ['module' => $moduleName, 'listenerId' => $listener->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <form action="{{ route('admin.modules.listeners.store', ['module' => $moduleName]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="event">Event Class</label>
                <input type="text" name="event" placeholder="e.g., Modules\Shop\Events\OrderCreated" required>
            </div>
            <div class="form-group">
                <label for="listener">Listener Class</label>
                <input type="text" name="listener" placeholder="e.g., Modules\{{ $moduleName }}\Listeners\ProcessPayment" required>
            </div>
            <button type="submit" class="btn">Add Listener</button>
        </form>

        <hr>

        <h3>Output Events (Dispatchers)</h3>
        <p>Define which events this module dispatches.</p>
        <table>
            <thead>
                <tr>
                    <th>Event</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>{{ $event->event }}</td>
                    <td>{{ $event->description }}</td>
                    <td>
                        <form action="{{ route('admin.modules.events.destroy', ['module' => $moduleName, 'eventId' => $event->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <form action="{{ route('admin.modules.events.store', ['module' => $moduleName]) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="event">Event Class</label>
                <input type="text" name="event" placeholder="e.g., Modules\{{ $moduleName }}\Events\PaymentProcessed" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" name="description">
            </div>
            <button type="submit" class="btn">Add Event</button>
        </form>
    </div>

</body>
</html>
