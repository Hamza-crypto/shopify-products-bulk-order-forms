<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3>Meter Readings for <span id="meter-name-title">Meter 1</span></h3>
            <table class="table table-striped" border>
                <thead>
                    <tr>
                        <th>Reading</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="meter-readings-table-body">
                    <!-- Table rows will be inserted here via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Function to format date to YYYY-MM-DD
        function formatDate(dateString) {
            const options = {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('en-CA', options); // 'en-CA' for YYYY-MM-DD format
        }


        // Function to fetch and display meter readings
        function fetchMeterReadings(meterName) {
            fetch(`/meter-readings/${meterName}`)
                .then(response => response.json())
                .then(data => {
                    const tableBody = document.getElementById('meter-readings-table-body');
                    tableBody.innerHTML = '';
                    data.forEach(reading => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                        <td>${reading.reading_value}</td>
                        <td>${formatDate(reading.created_at)}</td>
                        <td>
                            <form action="/meter-readings/${reading.id}" method="POST" onsubmit="return confirm('Are you sure you want to delete this reading?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </td>
                    `;
                        tableBody.appendChild(row);
                    });
                });
        }

        // Fetch meter readings for the selected meter
        const meterNameSelect = document.getElementById('meter_name');
        meterNameSelect.addEventListener('change', function() {
            const selectedMeter = meterNameSelect.value;
            document.getElementById('meter-name-title').innerText = `Meter ${selectedMeter}`;
            fetchMeterReadings(selectedMeter);
        });

        // Initial fetch for default selected meter
        fetchMeterReadings(meterNameSelect.value);
    });
</script>
