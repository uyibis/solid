<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusher Trade Events</title>

    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Pusher -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Enable Pusher logging (for debugging)
            Pusher.logToConsole = true;

            // Initialize Pusher
            var pusher = new Pusher('eb29561c2e520c1a3a36', {
                cluster: 'mt1'
            });

            // Subscribe to the channel
            var channel = pusher.subscribe('trade-channel');

            // Listen for the new trade event
            channel.bind('new-trade', function (data) {
                console.log("New Trade Event Received:", data);
                addTradeToTable(data);
            });

            function addTradeToTable(trade) {
                var tableBody = document.getElementById("tradeTableBody");

                var row = document.createElement("tr");
                row.setAttribute("data-master-id", trade.master_id);

                row.innerHTML = `
                    <td>${trade.master_id}</td>
                    <td>${trade.slave_id}</td>
                    <td>${trade.ip_address}</td>
                    <td>${new Date(trade.created_at).toLocaleString()}</td>
                    <td><button class="btn btn-danger btn-sm" onclick="removeMaster('${trade.master_id}', this)">Remove</button></td>
                `;

                tableBody.prepend(row); // Add the newest trade at the top
            }

            // Function to remove the master trader
            window.removeMaster = function (masterId, button) {
                if (confirm("Are you sure you want to remove this master trader?")) {
                    fetch(`{{assert('remove-master')}}/${masterId}`, {
                        method: "GET",
                        headers: {
                            "Content-Type": "application/json"
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("Failed to remove master trader");
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log(data.message);
                            // Remove the row from the table
                            button.closest("tr").remove();
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            alert("Error removing master trader");
                        });
                }
            };
        });
    </script>
</head>
<body class="container mt-5">

<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Navbar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
<!--                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="#">Home</a>
                </li>-->
<!--                <li class="nav-item">
                    <a class="nav-link" href="#">Link</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link disabled" aria-disabled="true">Disabled</a>
                </li>-->
            </ul>
            <div class="d-flex">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline-dark me-2">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-dark">Log in</a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</nav>

<h2 class="mb-4 text-center">Real-Time Trade Events</h2>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
    <tr>
        <th>Master ID</th>
        <th>Slave ID</th>
        <th>IP Address</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody id="tradeTableBody">
    <!-- New trade events will be inserted here -->
    </tbody>
</table>

<!-- Bootstrap JS (Optional, if you need interactive components like modals) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
