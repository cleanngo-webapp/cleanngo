import "./bootstrap";
import "remixicon/fonts/remixicon.css";
import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";

function applyTodayButtonStyles(container) {
    const btn = container.querySelector(".fc-today-button");
    if (btn) {
        btn.classList.add(
            "bg-emerald-700",
            "text-white",
            "hover:bg-emerald-800",
            "border-0"
        );
        btn.style.backgroundColor = "#047857";
        btn.style.color = "#ffffff";
    }
}

function renderCalendar(el) {
    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin],
        initialView: "dayGridMonth",
        height: "auto",
        events: el.dataset.eventsUrl,
        headerToolbar: { start: "prev,next today", center: "title", end: "" },
        buttonText: { today: "Today" },
        displayEventTime: false,
        // Fix event overflow issues
        dayMaxEvents: 3,
        moreLinkClick: "popover",
        eventDisplay: "block",
        eventDidMount: function (info) {
            const props = info.event.extendedProps || {};

            // Ensure events stay within their day cells
            info.el.style.overflow = "hidden";
            info.el.style.textOverflow = "ellipsis";
            info.el.style.whiteSpace = "nowrap";
            info.el.style.maxWidth = "100%";
            info.el.style.width = "100%";
            info.el.style.boxSizing = "border-box";
            info.el.style.cursor = "pointer";

            // Create tooltip content
            const content = document.createElement("div");
            content.className = "calendar-tooltip hidden";
            // Format status properly
            const formatStatus = (status) => {
                if (!status) return "—";
                return status
                    .replace(/_/g, " ")
                    .replace(/\b\w/g, (l) => l.toUpperCase());
            };

            // Format employee name
            const formatEmployee = (employee) => {
                if (!employee || employee.trim() === "" || employee === "—") {
                    return "Not Assigned Yet";
                }
                return employee;
            };

            content.innerHTML = `
                <div class="font-semibold text-sm mb-2 text-emerald-700">${
                    props.code || info.event.title
                }</div>
                <div class="space-y-1">
                    <div><span class="text-gray-500 font-medium">Date & Time:</span> <span class="text-gray-800">${
                        props.start_text || info.event.title
                    }</span></div>
                    <div><span class="text-gray-500 font-medium">Customer:</span> <span class="text-gray-800">${
                        props.customer_name || "—"
                    }</span></div>
                    <div><span class="text-gray-500 font-medium">Employee:</span> <span class="text-gray-800">${formatEmployee(
                        props.employee_name
                    )}</span></div>
                    <div><span class="text-gray-500 font-medium">Status:</span> <span class="text-gray-800">${formatStatus(
                        props.status
                    )}</span></div>
                </div>
            `;

            // Position tooltip relative to viewport to prevent overflow
            info.el.addEventListener("mouseenter", (e) => {
                content.classList.remove("hidden");

                // Get mouse position and viewport dimensions
                const mouseX = e.clientX;
                const mouseY = e.clientY;
                const viewportWidth = window.innerWidth;
                const viewportHeight = window.innerHeight;
                const tooltipWidth = 300; // max-width from CSS
                const tooltipHeight = 120; // estimated height

                // Calculate position to keep tooltip within viewport
                let left = mouseX + 10;
                let top = mouseY - tooltipHeight - 10;

                // Adjust if tooltip would go off right edge
                if (left + tooltipWidth > viewportWidth) {
                    left = mouseX - tooltipWidth - 10;
                }

                // Adjust if tooltip would go off top edge
                if (top < 10) {
                    top = mouseY + 10;
                }

                // Ensure tooltip doesn't go off left edge
                if (left < 10) {
                    left = 10;
                }

                content.style.left = left + "px";
                content.style.top = top + "px";

                document.body.appendChild(content);
            });

            info.el.addEventListener("mouseleave", () => {
                content.classList.add("hidden");
                if (content.parentNode) {
                    content.parentNode.removeChild(content);
                }
            });
        },
    });
    calendar.render();
    applyTodayButtonStyles(el);
    return calendar;
}

document.addEventListener("DOMContentLoaded", () => {
    const adminCal = document.getElementById("admin-calendar");
    if (adminCal) renderCalendar(adminCal);

    const employeeCal = document.getElementById("employee-calendar");
    if (employeeCal) renderCalendar(employeeCal);
});
