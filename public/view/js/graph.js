// Past 7 day work-time graph (displayed on dashboard page)
// ==========================================================================
{
    // chart data retrieving & manipulation
    const canvas = document.getElementById("work-time-graph");
    const parsed_data = JSON.parse(canvas.getAttribute("data-graph-data"));
    const days = [];

    // reverse data object since the rendering will start from the oldest day
    Object.keys(parsed_data).forEach(day => {
        days.unshift(day)
    })

    // get dynamic graph colors (which depend on project group selected in landing page)
    const bodyStyles = window.getComputedStyle(document.body);
    const lineColor = bodyStyles.getPropertyValue("--primary-color");
    const legendColor = bodyStyles.getPropertyValue("--section-text-color");

    // canvas preparation
    const c = canvas.getContext("2d");

    const width = canvas.width;
    const height = canvas.height;

    console.log(c);

    c.fillRect(25, 25, 100, 100);
    c.clearRect(45, 45, 60, 60);
    c.strokeRect(50, 50, 50, 50);


}   
