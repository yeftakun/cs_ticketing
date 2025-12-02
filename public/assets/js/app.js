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

// Notifikasi
(() => {
    const badge = document.getElementById("notif-badge");
    const modalEl = document.getElementById("notifModal");
    const listEl = document.getElementById("notif-list");
    const btn = document.getElementById("notif-btn");

    const renderNotif = (items) => {
        if (!listEl) return;
        listEl.innerHTML = "";
        if (!items || items.length === 0) {
            listEl.innerHTML = '<div class="text-center text-muted py-2">Tidak ada notifikasi baru.</div>';
            return;
        }
        items.forEach((item) => {
            const div = document.createElement("div");
            div.className = "list-group-item";
            div.innerHTML = `
                <div class="fw-semibold">${item.title || ""}</div>
                <div class="text-muted small">${item.created_at || ""}</div>
                <div class="text-muted">${item.message || ""}</div>
            `;
            listEl.appendChild(div);
        });
    };

    const fetchCount = () => {
        if (!badge) return;
        fetch("?ajax=notif-count", { headers: { "Accept": "application/json" } })
            .then((r) => r.json())
            .then((res) => {
                const c = parseInt(res.count || 0, 10);
                if (c > 0) {
                    badge.textContent = c;
                    badge.classList.remove("d-none");
                } else {
                    badge.classList.add("d-none");
                }
            })
            .catch(() => {});
    };

    const fetchUnread = () => {
        if (!listEl) return;
        listEl.innerHTML = '<div class="text-center text-muted py-2">Memuat...</div>';
        fetch("?ajax=notif-unread", { headers: { "Accept": "application/json" } })
            .then((r) => r.json())
            .then((res) => {
                renderNotif(res.items || []);
                fetchCount(); // update badge
            })
            .catch(() => {
                listEl.innerHTML = '<div class="text-center text-muted py-2">Gagal memuat notifikasi.</div>';
            });
    };

    if (btn && modalEl) {
        // Fetch count on load
        fetchCount();
        modalEl.addEventListener("show.bs.modal", fetchUnread);
    }
})();
