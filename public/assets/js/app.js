(() => {
    const sidebar = document.getElementById("sidebar");
    const backdrop = document.getElementById("sidebar-backdrop");
    const toggles = document.querySelectorAll("[data-toggle='sidebar']");

    const toggleSidebar = () => {
        if (!sidebar) return;
        sidebar.classList.toggle("collapsed");
        if (backdrop) {
            backdrop.classList.toggle("show", !sidebar.classList.contains("collapsed"));
        }
    };

    toggles.forEach((btn) => btn.addEventListener("click", toggleSidebar));
    if (backdrop) {
        backdrop.addEventListener("click", toggleSidebar);
    }
})();

(() => {
    if (typeof Chart === "undefined") return;

    const barEl = document.getElementById("kategoriChart");
    const trendEl = document.getElementById("trendChart");

    if (barEl) {
        const ctx = barEl.getContext("2d");
        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Jaringan", "Tagihan", "Produk", "Layanan Data", "Promo", "Perangkat", "Lainnya"],
                datasets: [
                    {
                        label: "Jumlah Keluhan",
                        data: [42, 35, 28, 30, 22, 18, 12],
                        backgroundColor: ["#b00020", "#0ea5e9", "#22c55e", "#f97316", "#6366f1", "#14b8a6", "#94a3b8"],
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    if (trendEl) {
        const ctx = trendEl.getContext("2d");
        new Chart(ctx, {
            type: "line",
            data: {
                labels: ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
                datasets: [
                    {
                        label: "Keluhan Baru",
                        data: [32, 28, 30, 27, 35, 25, 22],
                        borderColor: "#b00020",
                        backgroundColor: "rgba(176, 0, 32, 0.12)",
                        tension: 0.35,
                        borderWidth: 3,
                        fill: true
                    },
                    {
                        label: "Keluhan Selesai",
                        data: [18, 20, 21, 24, 28, 20, 18],
                        borderColor: "#0ea5e9",
                        backgroundColor: "rgba(14, 165, 233, 0.12)",
                        tension: 0.35,
                        borderWidth: 3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: "bottom" }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
})();
