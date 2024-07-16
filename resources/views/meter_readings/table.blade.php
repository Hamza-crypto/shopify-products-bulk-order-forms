<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h3>Meter Readings for <span id="meter-name-title">Meter 1</span></h3>
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Reading</th>
                        <th>Date</th>
                        <th>Units Consumed</th>
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
                    data.forEach((reading, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                                <td class="editable" data-id="${reading.id}" data-field="reading_value">${reading.reading_value}</td>
                                <td>${formatDate(reading.created_at)}</td>
                                <td>${reading.difference !== null ? reading.difference : '-'}</td>
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

                    // Add event listeners for editable cells
                    const editableCells = document.querySelectorAll('.editable');
                    editableCells.forEach(cell => {
                        cell.addEventListener('touchstart', handleCellTouchStart);
                        cell.addEventListener('dblclick', handleCellDblClick);
                    });
                });
        }

        // Function to handle touch start event on a cell
        function handleCellTouchStart(event) {
            event.preventDefault();
            handleCellDblClick(event);
        }

        // Function to handle double-click event on a cell
        function handleCellDblClick(event) {
            const cell = event.target;
            const currentValue = cell.textContent;
            const input = document.createElement('input');
            input.type = 'number';
            input.value = currentValue;
            input.className = 'form-control editable-input';
            cell.innerHTML = '';
            cell.appendChild(input);
            input.focus();

            input.addEventListener('blur', function() {
                const newValue = input.value;
                const readingId = cell.getAttribute('data-id');
                const field = cell.getAttribute('data-field');

                // Update the cell value in the database via AJAX
                updateReadingValue(readingId, field, newValue)
                    .then(() => {
                        cell.innerHTML = newValue;
                        toastr.success('Reading updated successfully');
                    })
                    .catch(err => {
                        console.error(err);
                        cell.innerHTML = currentValue; // Revert to original value on error
                        toastr.error('Failed to update reading');
                    });
            });

            input.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    input.blur();
                }
            });
        }

        // Function to update reading value via AJAX
        async function updateReadingValue(id, field, value) {
            const response = await fetch(`/meter-readings/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content')
                },
                body: JSON.stringify({
                    [field]: value
                })
            });

            if (!response.ok) {
                throw new Error('Failed to update reading value');
            }

            return response.json();
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
