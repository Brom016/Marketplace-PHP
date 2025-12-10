document.addEventListener("DOMContentLoaded", () => {

    if (typeof statsData === "undefined") {
        console.error("statsData tidak ditemukan!");
        return;
    }

    const ctx = document.getElementById('statsChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Users', 'Products', 'Sold'],
            datasets: [{
                label: 'Total',
                data: [
                    statsData.users,
                    statsData.products,
                    statsData.sold
                ],
                borderWidth: 1,
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#f6c23e'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { y: { beginAtZero: true } }
        }
    });

});
