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
        eventDidMount: function (info) {
            const props = info.event.extendedProps || {};
            const content = document.createElement("div");
            content.className =
                "absolute z-[999] hidden bg-white border rounded shadow p-2 text-xs";
            content.style.minWidth = "220px";
            content.innerHTML = `
                <div class="font-semibold mb-1">${
                    props.code || info.event.title
                }</div>
                <div><span class="text-gray-500">When:</span> ${
                    props.start_text || info.event.title
                }</div>
                <div><span class="text-gray-500">Employee:</span> ${
                    props.employee_name || "—"
                }</div>
                <div><span class="text-gray-500">Customer:</span> ${
                    props.customer_name || ""
                }</div>
                <div><span class="text-gray-500">Status:</span> ${
                    props.status || ""
                }</div>
            `;
            info.el.style.position = "relative";
            info.el.appendChild(content);
            info.el.addEventListener("mouseenter", () => {
                content.classList.remove("hidden");
            });
            info.el.addEventListener("mouseleave", () => {
                content.classList.add("hidden");
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
