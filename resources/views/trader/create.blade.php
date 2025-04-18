@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h2>Add Trader</h2>

        <form id="traderForm">
            @csrf

            <div class="mb-3">
                <label for="trader_code" class="form-label">Trader Code(s)</label>
                <div class="input-group">
                    <textarea class="form-control" id="trader_code" name="trader_code" rows="2" readonly required></textarea>
                    <button type="button" class="btn btn-primary" id="generateTrader">Add Trader</button>
                </div>
                <small class="form-text text-muted">You can add multiple trader codes. They will be separated by semicolons (;)</small>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email (Optional)</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone Number (Optional)</label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>

            <button type="submit" class="btn btn-success">Submit</button>
        </form>

        <div id="responseContainer" class="mt-3"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Generate Trader Button Click
            document.getElementById("generateTrader").addEventListener("click", function () {
                fetch("{{ route('trader.create') }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.code) {
                            const codeInput = document.getElementById("trader_code");
                            let existing = codeInput.value.trim();
                            if (existing.length > 0) {
                                existing += ";";
                            }
                            codeInput.value = existing + data.code;
                        } else {
                            alert("Failed to generate trader. Try again.");
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Error generating trader.");
                    });
            });

            // Handle Form Submission
            document.getElementById("traderForm").addEventListener("submit", function (event) {
                event.preventDefault();
                let formData = new FormData(this);

                fetch("{{ route('master.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name=\"_token\"]').value,
                    },
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        let responseContainer = document.getElementById("responseContainer");
                        if (data.success) {
                            responseContainer.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                            document.getElementById("traderForm").reset();
                            document.getElementById("trader_code").value = '';
                        } else {
                            responseContainer.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        alert("Error submitting form.");
                    });
            });
        });
    </script>
@endsection
