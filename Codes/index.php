document.addEventListener('DOMContentLoaded', function() {
  var calendarEl = document.getElementById('calendar');

  var calendar = new FullCalendar.Calendar(calendarEl, {
    // ... other options ...

    events: [
      // Weekly Repeating Event
      {
        id: 'weeklyEvent',
        title: 'Weekly Event',
        start: '2023-06-01T10:00:00',
        end: '2023-06-01T11:00:00',
        daysOfWeek: [2], // Recur on Tuesday
        startTime: '10:00:00',
        endTime: '11:00:00',
        color: 'blue',
        textColor: 'white',
      },

      // Biweekly Repeating Event
      {
        id: 'biweeklyEvent',
        title: 'Biweekly Event',
        start: '2023-06-01T14:00:00',
        end: '2023-06-01T15:00:00',
        daysOfWeek: [4], // Recur on Thursday
        startTime: '14:00:00',
        endTime: '15:00:00',
        color: 'green',
        textColor: 'white',
        duration: { weeks: 2 } // Repeat every 2 weeks
      },

      // Monthly Repeating Event
      {
        id: 'monthlyEvent',
        title: 'Monthly Event',
        start: '2023-06-01T16:00:00',
        end: '2023-06-01T17:00:00',
        startTime: '16:00:00',
        endTime: '17:00:00',
        color: 'purple',
        textColor: 'white',
        startRecur: '2023-06-01', // Start recurring from June 1, 2023
        endRecur: '2023-12-31', // End recurring on December 31, 2023
        daysOfMonth: [15] // Recur on the 15th day of each month
      },

      // Bimonthly Repeating Event
      {
        id: 'bimonthlyEvent',
        title: 'Bimonthly Event',
        start: '2023-06-01T18:00:00',
        end: '2023-06-01T19:00:00',
        startTime: '18:00:00',
        endTime: '19:00:00',
        color: 'orange',
        textColor: 'white',
        startRecur: '2023-06-01', // Start recurring from June 1, 2023
        endRecur: '2023-12-31', // End recurring on December 31, 2023
        interval: 2 // Repeat every 2 months
      }
    ],

    // ... other event handling code ...
  });

  calendar.render();
});
