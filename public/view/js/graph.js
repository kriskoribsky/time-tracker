// Past 7 day work-time graph (displayed on dashboard page)
// ==========================================================================
{
    // returns the closest upper bound which is both divisible by multiple & one of line counts
    function getUpperBound(currentMax, multiple, intervals) {
        // round up to multiple
        currentMax = Math.ceil(currentMax / multiple) * multiple;

        while (true) {
            for (let i=0; i < intervals.length; i++) {
                if (currentMax % intervals[i] === 0) {
                    return [currentMax, currentMax / intervals[i]];
                }
            }
            // if none of intervals are divisors, increase max by multiple
            currentMax += multiple;
        }
    }
    // calculates unit intervals on Yaxis (hrs or min)
    function getYunits(seconds) {
        // get max seconds and convert to minutes
        max = seconds === 0 ? 1 : Math.max(...seconds);
        max = Math.ceil(max / 60);

        // how many horizontal lines graph can have (more = better spread)
        const intervals = [4, 3, 5];
        const minuteIncrements = 5; // 5 minute increment 
        const hourIncrements = 0.5; // half hour increment

        let units = [];
        // minutes
        if (max <= 60) {
            var [upperBound, increment] = getUpperBound(max, minuteIncrements, intervals);

            for (let i=0; i <= upperBound; i += increment) {
            units.push(i + " " + minutesPostfix);
            }

            upperBound *= 60;

        // hours
        } else {
            var [upperBound, increment] = getUpperBound(max / 60, hourIncrements, intervals);

            for (let i=0; i <= upperBound; i += increment) {
            units.push(i + " " + hoursPostfix);
            }

            // convert back to seconds for returning value
            upperBound *= 60 * 60;

        }
        return [units, upperBound];
    }

    // chart data retrieving & manipulation
    // ==========================================================================
    const canvas = document.getElementById("work-time-graph");
    const parsed_data = JSON.parse(canvas.getAttribute("data-graph-data"));
    const keys = Object.keys(parsed_data);
    const values = Object.values(parsed_data).reverse();
    let units = [];
    let upperBound;
    const minutesPostfix = 'min';
    const hoursPostfix = 'h';

    // reverse data object since the rendering will start from the oldest day
    units["x"] = keys.reverse();
    [units["y"], upperBound] = getYunits(values);

    const longestYunit = (() => {
        max = units["y"][0];

        for (let i=1; i < units["y"].length; i++) {
            if (units["y"][i].length > max.length) {
                max = units["y"][i];
            }
        }
        return max;
    })();

    console.log("xUnits:", units["x"]);
    console.log("values:", values);
    console.log("yUnits:", units["y"]);

    // get dynamic graph colors (which depend on project group selected in landing page)
    const bodyStyles = window.getComputedStyle(document.body);
    const lineColor = bodyStyles.getPropertyValue("--primary-color");
    const legendColor = bodyStyles.getPropertyValue("--section-text-color");

    const legendFont = "Roboto, -apple-system, sans-serif";

    // 5% padding between graph objects
    const internalPadding = 0.05;
    // pixel count increase on-canvas to improve crispiness
    const res = 2;

    // canvas rendering
    // ==========================================================================
    // round down to nearest pixel to prevent blurry look
    function floorPixel(pixels) {
        return Math.floor(pixels);
    }

    function ceilPixel(pixels) {
        return Math.ceil(pixels);
    } 

    function triangleCentroid(x1, x2, y1 ,y2, z1, z2) {
        // centroid of a triagle is anarithmetic mean of its x and y coordinates
        tx = (x1 + y1 + z1) / 3;
        ty = (x2 + y2 + z2) / 3;

        return [tx, ty];
    }

    function cubicBezier(x1, y1, x2, y2) {
        // cubic bezier control points for graph will be
        // centroids of their right triangles from midpoint

        // midpoint coords
        sx = Math.abs(x2 + x1) / 2;
        sy = Math.abs(y2 + y1) / 2;

        // control point 1 (the last 2 coordinates form the foot of the right triangle)
        const [cp1x, cp1y] = triangleCentroid(x1, y1, sx, sy, sx, y1);
        const [cp2x, cp2y] = triangleCentroid(x2, y2, sx, sy, sx, y2);


        return [cp1x, cp1y, cp2x, cp2y, x2, y2];
    }

    function quadraticBezier(x1, y1, x2, y2) {
        
        console.log(`x1: ${x1} y1: ${y1} x2: ${x2} y2: ${y2}`);

        // midpoint coords
        sx = Math.abs(x2 + x1) / 2;
        sy = Math.abs(y2 + y1) / 2;

        console.log(`sx: ${sx} sy: ${sy}`);

        return [sx, sy, x2, y2];
    }

    function renderGraph(width, height, ratio) {
        const ctx = canvas.getContext("2d");

        // calculate graph padding
        const p = ceilPixel(internalPadding * Math.max(canvas.width, canvas.height));
        // gap betwwen graph elements
        const g = ceilPixel(p / 2);

        // dynamic sizes
        ctx.font = (12 * ratio) + "px " + legendFont;
        const radius = ceilPixel((p / 16) * ratio);
        const mainLineWidth = floorPixel(radius / 2) + 0.5;
        const horizontalLineWidth = 0.5;
        const horizontalLineDash = [3 * ratio, 3 * ratio];

        // Render Y axis
        // ==========================================================================
        ctx.fillStyle = legendColor;
        ctx.strokeStyle = legendColor;
        ctx.lineWidth = horizontalLineWidth;
        ctx.setLineDash(horizontalLineDash);
        ctx.textBaseline = "middle";
        ctx.textAlign = "end";

        const startY = p;
        // measure required horizontal space
        const textWidth = ceilPixel(ctx.measureText(longestYunit).width);
        const yIncrement = floorPixel((height - 2 * p) / (units["y"].length-1));

        let y = startY;
        for (let i=0; i < units["y"].length; i++) {
            // y axis legend text
            ctx.fillText(units["y"][i], textWidth, height - y);

            // dashed line with aplha value
            ctx.globalAlpha = 0.7;

            ctx.beginPath();
            // little shift (g/2) to the right for the xAxis legend
            ctx.moveTo(textWidth + g - g/2, height - y);
            ctx.lineTo(width - p + g/2, height - y);
            ctx.stroke();
            ctx.globalAlpha = 1;

            y += yIncrement;
        }
        const endY = y - yIncrement;

        // Render X axis
        // ==========================================================================
        ctx.textBaseline = "ideographic";
        ctx.textAlign = "center";

        let x = textWidth + g;
        const xIncrement = floorPixel((width - x - p) / (units["x"].length-1));
        // used for later rendering of graph line
        var points = [];

        for (let i=0; i < units["x"].length; i++) {
            // calculate relative pos for later use when drawing graph main lines
            let pos = {};
            pos.x = x;
            pos.y = height - (startY + (values[i] / upperBound) * (endY - startY));
            
            points.push(pos);

            // legend text
            ctx.beginPath();
            ctx.fillStyle = legendColor;
            ctx.fillText(units["x"][i], x, height - p + floorPixel(p));

            x += xIncrement;
        }
        // Main graph columns
        // ==========================================================================
        ctx.strokeStyle = lineColor;
        ctx.fillStyle = lineColor;
        ctx.lineWidth = mainLineWidth;
        ctx.setLineDash([]);
        ctx.beginPath();

        // move to the first point
        ctx.moveTo(points[0].x, points[0].y);

        for (var i=1; i < points.length - 2; i++) {
            
        }
    }

    function updateCanvasSize() {
        // keep the canvas rendering crisp even on higher pixel ratio (e.g. when zooming)
        const ratio = window.devicePixelRatio * res;

        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;

        renderGraph(canvas.width, canvas.height, ratio);
    }
    // update canvas everytime the parent size monitor div changes size
    const sizeMonitorDiv = canvas.parentElement;
    new ResizeObserver(updateCanvasSize).observe(sizeMonitorDiv);
}   
