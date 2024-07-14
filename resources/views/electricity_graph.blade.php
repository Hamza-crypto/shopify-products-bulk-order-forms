<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Consumption Graph</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <h1>Electricity Consumption Graph</h1>

    <div>
        <canvas id="meter1Chart"></canvas>
        <h3>Total Units Consumed by Meter 1: {{ $totalMeter1 }}</h3>
    </div>

    <div>
        <canvas id="meter2Chart"></canvas>
        <h3>Total Units Consumed by Meter 2: {{ $totalMeter2 }}</h3>
    </div>

    <script>
        // Meter 1 data
        const meter1Labels = {!! json_encode(array_column($meter1Data, 'date')) !!};
        const meter1Data = {!! json_encode(array_column($meter1Data, 'usage')) !!};

        // Meter 2 data
        const meter2Labels = {!! json_encode(array_column($meter2Data, 'date')) !!};
        const meter2Data = {!! json_encode(array_column($meter2Data, 'usage')) !!};

        // Create chart for Meter 1
        const meter1Ctx = document.getElementById('meter1Chart').getContext('2d');
        new Chart(meter1Ctx, {
            type: 'bar',
            data: {
                labels: meter1Labels,
                datasets: [{
                    label: 'Meter 1 Daily Usage (units)',
                    data: meter1Data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Create chart for Meter 2
        const meter2Ctx = document.getElementById('meter2Chart').getContext('2d');
        new Chart(meter2Ctx, {
            type: 'bar',
            data: {
                labels: meter2Labels,
                datasets: [{
                    label: 'Meter 2 Daily Usage (units)',
                    data: meter2Data,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>

</html>
