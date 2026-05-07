(function () {
    var cfg = window.naviwpAdminData || {};
    var AJAX_URL = cfg.ajaxUrl || "";
    var NONCE = cfg.nonce || "";
    var REGISTER_NONCE = cfg.registerNonce || "";
    var APP_URL = cfg.appUrl || "";
    var ADD_MENU_URL = cfg.addMenuUrl || "";
    var EMPTY_STATE_IMG = cfg.emptyStateImg || "";
    var PRODUCT_NAME_ALIAS = cfg.productNameAlias || "Navi+ Menu Builder";

    function escHtml(str) {
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;");
    }

    function initRegisterAction() {
        var registerBtn = document.getElementById("navi-register-btn");
        if (!registerBtn) {
            return;
        }

        registerBtn.addEventListener("click", function () {
            var btn = this;
            btn.disabled = true;
            btn.textContent = "Initializing the application…";

            var msg = document.getElementById("navi-message");
            if (msg) {
                msg.className = "navi-notice";
                msg.style.display = "none";
            }

            fetch(AJAX_URL, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({
                    action: "naviwp_register",
                    nonce: REGISTER_NONCE
                })
            })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.success) {
                        sessionStorage.setItem("navi_just_registered", "1");
                        location.reload();
                        return;
                    }
                    if (msg) {
                        msg.className = "navi-notice error";
                        msg.textContent = "Error: " + res.data;
                        msg.style.display = "block";
                    }
                    btn.disabled = false;
                    btn.textContent = "Create your first menu";
                })
                .catch(function () {
                    if (msg) {
                        msg.className = "navi-notice error";
                        msg.textContent = "Could not reach the server. Please try again.";
                        msg.style.display = "block";
                    }
                    btn.disabled = false;
                    btn.textContent = "Create your first menu";
                });
        });
    }

    function initLinkedSiteActions() {
        var tableBody = document.getElementById("navi-table-body");
        if (!tableBody) {
            return;
        }

        var KIND_LABELS = {
            1: "Sticky / Bottom, Tab bar (Mobile + Desktop)",
            2: "Sticky / Mobile header",
            11: "Sticky / FAB, Support bar",
            20: "Section / Mobile header",
            31: "Section / Mobile Megamenu",
            41: "Section / Mobile grid menu",
            42: "Section / Mobile banner",
            131: "Section / Desktop Megamenu",
            141: "Context / Slide menu"
        };
        var SHORTCODE_KINDS = [31, 41, 131];

        document.addEventListener("click", function (e) {
            if (!e.target.closest(".navi-dropdown")) {
                document.querySelectorAll(".navi-dropdown-menu.open").forEach(function (m) {
                    m.classList.remove("open");
                });
            }
        });

        function loadMenus() {
            tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:#646970;"><span class="navi-spin"></span> Loading…</td></tr>';
            var countEl = document.getElementById("navi-table-count");
            if (countEl) {
                countEl.textContent = "";
            }

            fetch(AJAX_URL, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ action: "naviwp_get_menus", nonce: NONCE })
            })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (!res.success) {
                        tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:#d63638;">Error: ' + escHtml(res.data) + "</td></tr>";
                        return;
                    }
                    var menus = res.data.menus || [];
                    if (countEl) {
                        countEl.textContent = "(" + menus.length + ")";
                    }

                    if (!menus.length) {
                        tableBody.innerHTML = '<tr><td colspan="5"><div class="navi-empty-state">' +
                            '<img src="' + EMPTY_STATE_IMG + '" alt="No menus yet">' +
                            "<h3>No menu has been created yet. Let's get started!</h3>" +
                            "<p>Click <strong>Create new menu</strong> to get started &mdash; don't worry, we'll guide you step by step!</p>" +
                            '<div class="navi-empty-actions">' +
                            '<a id="navi-create-menu-btn" href="' + ADD_MENU_URL + '" target="_blank" class="navi-btn">+ Add menu</a>' +
                            '<a href="https://naviplus.io/demo/" target="_blank" class="navi-btn navi-btn-secondary">View example</a>' +
                            "</div></div></td></tr>";

                        if (sessionStorage.getItem("navi_just_registered")) {
                            sessionStorage.removeItem("navi_just_registered");
                            var notice = document.getElementById("navi-welcome-notice");
                            if (notice) {
                                notice.style.display = "block";
                            }
                            setTimeout(function () {
                                var btn = document.getElementById("navi-create-menu-btn");
                                if (btn) {
                                    btn.classList.add("navi-btn-pulse");
                                }
                            }, 100);
                        }
                        return;
                    }

                    tableBody.innerHTML = menus.map(function (menu) {
                        var visibleBadge = '<span class="navi-badge navi-badge-blue">' + escHtml(String(menu.visible)) + "</span>";
                        var updated = menu.updated_date ? menu.updated_date.substring(0, 10) : "—";
                        var hasShortcode = SHORTCODE_KINDS.indexOf(menu.kind) !== -1;
                        var sc = "[naviwp embed_id=&quot;" + menu.embed_id + "&quot;]";
                        var editUrl = ADD_MENU_URL.replace("deeplink=addMenu", "deeplink=editMenu[" + menu.id + "]");
                        var dropdownItems = "";

                        if (hasShortcode) {
                            dropdownItems = '<button class="navi-dd-copy" onclick="naviCopyShortcode(\'' + menu.embed_id + "')\">" +
                                "Copy shortcode " + sc + "</button>";
                        } else {
                            dropdownItems = '<span style="display:block;padding:8px 12px;font-size:12px;color:#646970;font-style:italic;max-width:240px;white-space:normal;">' +
                                "Note: Will be displayed when &ldquo;Embed " + escHtml(PRODUCT_NAME_ALIAS) + " into website&rdquo; toggle is enabled." +
                                "</span>";
                        }

                        var thumb = menu.thumbnail_url
                            ? '<img src="' + escHtml(menu.thumbnail_url) + '" alt="" style="width:42px;border-radius:2px;flex-shrink:0;" onerror="this.style.display=\'none\'">'
                            : "";

                        return "<tr>" +
                            '<td><div style="display:flex;align-items:center;gap:10px;">' +
                            thumb +
                            "<div><strong>" + escHtml(menu.name) + "</strong><br>" +
                            '<span style="font-size:12px;color:#a7aaad;">' + escHtml(menu.embed_id) + "</span></div></div></td>" +
                            '<td><span class="navi-badge navi-badge-blue">' + escHtml(KIND_LABELS[menu.kind] || menu.kind_label || "Unknown") + "</span></td>" +
                            "<td>" + visibleBadge + "</td>" +
                            '<td style="color:#646970;">' + escHtml(updated) + "</td>" +
                            '<td><div class="navi-actions">' +
                            '<a href="' + editUrl + '" target="_blank" class="navi-btn navi-btn-secondary navi-btn-sm">Edit</a>' +
                            '<div class="navi-dropdown"><button class="navi-dropdown-toggle" onclick="naviToggleDropdown(this)">···</button>' +
                            '<div class="navi-dropdown-menu">' + dropdownItems + "</div></div></div></td>" +
                            "</tr>";
                    }).join("");
                })
                .catch(function () {
                    tableBody.innerHTML = '<tr><td colspan="5" style="text-align:center;padding:28px;color:#d63638;">Could not reach the server. Please try again.</td></tr>';
                });
        }

        window.naviToggleDropdown = function (btn) {
            var menu = btn.nextElementSibling;
            var isOpen = menu.classList.contains("open");
            document.querySelectorAll(".navi-dropdown-menu.open").forEach(function (m) { m.classList.remove("open"); });
            if (!isOpen) {
                var r = btn.getBoundingClientRect();
                menu.style.top = (r.bottom + 4) + "px";
                menu.style.left = (r.right - 240) + "px";
                menu.classList.add("open");
            }
        };

        window.naviDeleteMenu = function (btn, embedId) {
            document.querySelectorAll(".navi-dropdown-menu.open").forEach(function (m) { m.classList.remove("open"); });
            if (!confirm('Delete "' + embedId + '"?\nThis action cannot be undone.')) {
                return;
            }
            btn.disabled = true;
            btn.textContent = "Deleting…";
            fetch(AJAX_URL, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ action: "naviwp_delete_menu", nonce: NONCE, embed_id: embedId })
            })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.success) {
                        var row = btn.closest("tr");
                        row.style.transition = "opacity 0.25s";
                        row.style.opacity = "0";
                        setTimeout(function () { row.remove(); }, 260);
                    } else {
                        alert("Error: " + res.data);
                        btn.disabled = false;
                        btn.textContent = "Delete";
                    }
                })
                .catch(function () {
                    alert("Could not reach the server. Please try again.");
                    btn.disabled = false;
                    btn.textContent = "Delete";
                });
        };

        window.naviCopyShortcode = function (embedId) {
            document.querySelectorAll(".navi-dropdown-menu.open").forEach(function (m) { m.classList.remove("open"); });
            var shortcode = '[naviwp embed_id="' + embedId + '"]';
            navigator.clipboard.writeText(shortcode).then(function () {
                var msg = document.getElementById("navi-copy-msg");
                if (msg) {
                    msg.style.display = "block";
                    setTimeout(function () { msg.style.display = "none"; }, 2500);
                }
            });
        };

        var refreshBtn = document.getElementById("navi-refresh-btn");
        if (refreshBtn) {
            refreshBtn.addEventListener("click", loadMenus);
        }

        var embedToggle = document.getElementById("navi-embed-toggle");
        if (embedToggle) {
            embedToggle.addEventListener("change", function () {
                var enabled = this.checked ? "1" : "0";
                var savingEl = document.getElementById("navi-embed-saving");
                if (savingEl) {
                    savingEl.innerHTML = '<span style="color:#646970;font-size:11px;">Saving…</span>';
                }

                fetch(AJAX_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: new URLSearchParams({ action: "naviwp_toggle_embed", nonce: NONCE, enabled: enabled })
                })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (!savingEl) {
                            return;
                        }
                        if (res.success) {
                            savingEl.innerHTML = enabled === "1"
                                ? '<span class="navi-badge navi-badge-green" id="navi-embed-status">Active</span>'
                                : '<span class="navi-badge navi-badge-gray" id="navi-embed-status">Disabled</span>';
                        } else {
                            savingEl.innerHTML = '<span style="color:#d63638;font-size:11px;">Error saving.</span>';
                        }
                    })
                    .catch(function () {
                        if (savingEl) {
                            savingEl.innerHTML = '<span style="color:#d63638;font-size:11px;">Error saving.</span>';
                        }
                    });
            });
        }

        function fmtNum(n) {
            return String(Math.floor(n || 0)).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function fmtDate(s) {
            if (!s) {
                return "—";
            }
            var p = s.split(" ");
            var d = p[0] ? p[0].substring(5).replace("-", "/") : "";
            var t = p[1] ? p[1].substring(0, 5) : "";
            return d + " " + t;
        }

        function apiCall(analyticsAction, extra, cb) {
            var params = { action: "naviwp_analytics", nonce: NONCE, analytics_action: analyticsAction };
            if (extra) {
                Object.keys(extra).forEach(function (k) { params[k] = extra[k]; });
            }
            fetch(AJAX_URL, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams(params)
            })
                .then(function (r) { return r.json(); })
                .then(function (res) { cb(res.success ? res.data : null); })
                .catch(function () { cb(null); });
        }

        function col(label, value, sub) {
            return '<div class="navi-stat-box">' +
                '<div class="navi-stat-label">' + label + "</div>" +
                '<div class="navi-stat-value">' + value + "</div>" +
                (sub ? '<div class="navi-stat-sub">' + sub + "</div>" : "") +
                "</div>";
        }

        function badge(text, cls) {
            return '<span class="navi-badge ' + cls + '">' + escHtml(text) + "</span>";
        }

        function renderSummary(data) {
            var el = document.getElementById("navi-analytics-stats");
            if (!el) {
                return;
            }
            var dash = "——";
            if (!data) {
                el.innerHTML = col("Navigation Events", dash) + col("Total Visits", dash) + col("Menu Created", dash) + col("SEO Support", dash) + col("Service Status", dash);
                return;
            }
            var nav = data.navigation_events || {};
            var visits = data.total_visits || {};
            var menus = data.menu_created || {};
            var seoClass = data.seo_support === "ON" ? "navi-badge-green" : "navi-badge-gray";
            var svcClass = data.service_status === "Very good" ? "navi-badge-green" : "navi-badge-blue";
            var planEl = document.getElementById("navi-account-plan");
            if (planEl && data.plan) {
                var planCls = { Elite: "navi-badge-green", Business: "navi-badge-blue", Starter: "navi-badge-gray" };
                var pc = planCls[data.plan] || "navi-badge-gray";
                planEl.innerHTML = '<a href="https://naviplus.io/pricing/" target="_blank" style="text-decoration:none;"><span class="navi-badge ' + pc + '">' + escHtml(data.plan) + "</span></a>";
            }
            el.innerHTML =
                col("Navigation Events", nav.value !== undefined ? fmtNum(nav.value) : dash, nav.period ? escHtml(nav.period) : "") +
                col("Total Visits", visits.value !== undefined ? fmtNum(visits.value) : dash, visits.limit ? "of " + fmtNum(visits.limit) : "") +
                col("Menu Created", menus.value !== undefined ? fmtNum(menus.value) : dash, menus.limit ? "of " + menus.limit : "") +
                col("SEO Support", data.seo_support ? badge(data.seo_support, seoClass) : dash) +
                col("Service Status", data.service_status ? badge(data.service_status, svcClass) : dash);
        }

        function renderChart(rows) {
            var el = document.getElementById("navi-analytics-chart");
            if (!el) {
                return;
            }
            if (!rows || !rows.length) {
                el.innerHTML = '<div class="navi-chart-empty">No click data for this period.</div>';
                return;
            }
            var max = Math.max.apply(null, rows.map(function (r) { return r.clicks; }));
            if (max === 0) {
                max = 1;
            }
            var n = rows.length;
            var every = n > 21 ? 7 : (n > 10 ? 5 : (n > 6 ? 3 : 1));
            var html = "";
            rows.forEach(function (r, i) {
                var pct = Math.max(2, Math.round((r.clicks / max) * 100));
                var lbl = (i % every === 0 || i === n - 1) ? escHtml(r.date.substring(5).replace("-", "/")) : "";
                html += '<div class="navi-bar-col" title="' + escHtml(r.date) + ": " + r.clicks + ' clicks">' +
                    '<div class="navi-bar-inner"><div class="navi-bar" style="height:' + pct + '%"></div></div>' +
                    '<div class="navi-bar-label">' + (lbl || "&nbsp;") + "</div></div>";
            });
            el.innerHTML = '<div class="navi-bar-wrap">' + html + "</div>";
        }

        function renderTopItems(items) {
            var el = document.getElementById("navi-analytics-top");
            if (!el) {
                return;
            }
            if (!items || !items.length) {
                el.innerHTML = '<div style="color:#646970;font-size:12px;padding:12px 0;">No data for this period.</div>';
                return;
            }
            var max = items[0].clicks || 1;
            el.innerHTML = items.slice(0, 10).map(function (item, i) {
                var pct = Math.round((item.clicks / max) * 100);
                return '<div class="navi-top-item"><div class="navi-top-item-row">' +
                    '<span class="navi-top-item-rank">' + (i + 1) + "</span>" +
                    '<span class="navi-top-item-name" title="' + escHtml(item.name) + '">' + escHtml(item.name) + "</span>" +
                    '<span class="navi-top-item-count">' + fmtNum(item.clicks) + "</span>" +
                    '</div><div class="navi-top-bar"><div class="navi-top-bar-fill" style="width:' + pct + '%"></div></div></div>';
            }).join("");
        }

        function renderRecentClicks(clicks) {
            var el = document.getElementById("navi-analytics-recent");
            if (!el) {
                return;
            }
            if (!clicks || !clicks.length) {
                el.innerHTML = '<div style="color:#646970;font-size:12px;padding:12px 0;">No recent clicks.</div>';
                return;
            }
            el.innerHTML = clicks.slice(0, 20).map(function (c) {
                var isMobile = c.platform === "M";
                return '<div class="navi-recent-item">' +
                    '<span class="navi-recent-platform ' + (isMobile ? "mobile" : "desktop") + '">' + (isMobile ? "M" : "D") + "</span>" +
                    '<div class="navi-recent-info">' +
                    '<div class="navi-recent-name" title="' + escHtml(c.item_name) + '">' + escHtml(c.item_name) + "</div>" +
                    '<div class="navi-recent-url" title="' + escHtml(c.from_url) + '">' + escHtml(c.from_url) + "</div></div>" +
                    '<div class="navi-recent-date">' + escHtml(fmtDate(c.action_date)) + "</div></div>";
            }).join("");
        }

        function loadAll() {
            var stats = document.getElementById("navi-analytics-stats");
            var chart = document.getElementById("navi-analytics-chart");
            var top = document.getElementById("navi-analytics-top");
            var recent = document.getElementById("navi-analytics-recent");

            if (stats) {
                stats.innerHTML = '<div style="width:100%;text-align:center;padding:8px;"><span class="navi-spin"></span></div>';
            }
            if (chart) {
                chart.innerHTML = '<div style="height:100%;display:flex;align-items:center;justify-content:center;color:#646970;gap:8px;"><span class="navi-spin"></span> Loading…</div>';
            }
            if (top) {
                top.innerHTML = '<span class="navi-spin"></span>';
            }
            if (recent) {
                recent.innerHTML = '<span class="navi-spin"></span>';
            }

            apiCall("summary", {}, renderSummary);
            apiCall("clicks-by-date", { days: 30 }, function (data) { renderChart(data ? data.rows : null); });
            apiCall("top-items", { days: 30, limit: 10 }, function (data) { renderTopItems(data ? data.items : null); });
            apiCall("recent-clicks", { limit: 20 }, function (data) { renderRecentClicks(data ? data.clicks : null); });
        }

        loadMenus();
        loadAll();
    }

    initRegisterAction();
    initLinkedSiteActions();
})();
