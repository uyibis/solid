<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BullinBear</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
        <!-- Customer Section -->
    <h4 class="mt-4">Customer Section</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
            <tr>
                <th>Customer Name</th>
                <th>Order#</th>
                <th>Connection Status</th>
                <th>Button To Block/Unblock</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Oscar</td>
                <td>F098908</td>
                <td>Connected</td>
                <td><input type="checkbox" class="form-check-input"></td>
            </tr>
            <tr>
                <td>Oscar</td>
                <td>F098908</td>
                <td>Connected</td>
                <td><input type="checkbox" class="form-check-input" checked></td>
            </tr>
            <tr>
                <td>Oscar</td>
                <td>F098908</td>
                <td>Connected</td>
                <td><input type="checkbox" class="form-check-input" checked></td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Master Section -->
    <h4 class="mt-4">Master Section</h4>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>No Of Master ID's</th>
                <th>No Of Slaves</th>
                <th>Button To Block/Unblock</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Oscar</td>
                <td>5</td>
                <td>2</td>
                <td><input type="checkbox" class="form-check-input"></td>
            </tr>
            <tr>
                <td>Oscar</td>
                <td>4</td>
                <td>3</td>
                <td><input type="checkbox" class="form-check-input" checked></td>
            </tr>
            <tr>
                <td>Oscar</td>
                <td>7</td>
                <td>4</td>
                <td><input type="checkbox" class="form-check-input" checked></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
