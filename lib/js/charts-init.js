window.addEventListener('DOMContentLoaded', function () {
  let charts = document.querySelectorAll('.admin-helper-chart');
  if (charts) {
    charts.forEach(function (el) {
      let data = el.getAttribute('data-chart');
      // console.log(JSON.parse(data));
      new Chart(el, JSON.parse(data));
    });
  }
});