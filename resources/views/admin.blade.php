<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Lucky Draw</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Lucky Draw Admin</h1>
        <a href="/" class="btn btn-primary mb-3">Back to Draw</a>

        <h2>Add New Prize</h2>
        <form action="/admin/prizes" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label>Photo</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" required min="1">
            </div>
            <div class="mb-3">
                <label>Order</label>
                <input type="number" name="order" class="form-control" required min="1">
            </div>
            <button type="submit" class="btn btn-success">Add Prize</button>
        </form>

        <h2 class="mt-5">Existing Prizes</h2>
        <div class="row">
            @foreach ($prizes as $prize)
                <div class="col-md-4 mb-3">
                    <div class="card">
                        @if ($prize->photo_path)
                            <img src="/storage/{{ $prize->photo_path }}" class="card-img-top" alt="{{ $prize->name }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $prize->name }}</h5>
                            <p class="card-text">{{ $prize->description }}</p>
                            <p>Quantity: {{ $prize->quantity }}</p>
                            <p>Winners: {{ $prize->winners()->count() }}</p>
                            <form action="/admin/prizes/{{ $prize->id }}" method="POST"
                                onsubmit="return confirm('Delete this prize?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>

</html>
