import "./bootstrap";
import "remixicon/fonts/remixicon.css";

// Lightweight FullCalendar integration: load only when target containers exist
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
