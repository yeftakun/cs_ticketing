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
    const data = window.dashboardData || {};

    if (barEl) {
        const ctx = barEl.getContext("2d");
        const labels = data.bar?.labels || [];
        const values = data.bar?.values || [];
        const hasData = Array.isArray(values) && values.some((v) => Number(v) > 0);
        if (!hasData) {
            barEl.parentElement.querySelector(".empty-state")?.classList.remove("d-none");
            return;
        }
        new Chart(ctx, {
            type: "bar",
            data: {
                labels,
                datasets: [
                    {
                        label: "Jumlah Keluhan",
                        data: values,
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
        const labels = data.trend?.labels || [];
        const values = data.trend?.values || [];
        const solved = data.trend?.solved || [];
        const hasData =
            (Array.isArray(values) && values.some((v) => Number(v) > 0)) ||
            (Array.isArray(solved) && solved.some((v) => Number(v) > 0));
        if (!hasData) {
            trendEl.parentElement.querySelector(".empty-state")?.classList.remove("d-none");
            return;
        }
        new Chart(ctx, {
            type: "line",
            data: {
                labels,
                datasets: [
                    {
                        label: "Keluhan Baru",
                        data: values,
                        borderColor: "#b00020",
                        backgroundColor: "rgba(176, 0, 32, 0.12)",
                        tension: 0.35,
                        borderWidth: 3,
                        fill: true
                    },
                    {
                        label: "Keluhan Selesai",
                        data: solved,
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
