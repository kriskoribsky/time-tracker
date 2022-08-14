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
    // rendering of y axis legend
    // function renderY(ctx, width, height, p, g) {
    //     ctx.fillStyle = legendColor;
    //     ctx.strokeStyle = legendColor;
    //     ctx.lineWidth = 0.5;
    //     ctx.setLineDash([3 * window.devicePixelRatio, 3 * window.devicePixelRatio]);
    //     ctx.textBaseline = "middle";
    //     ctx.textAlign = "end";

    //     const n = yUnits.length;
    //     const startY = p;

    //     // measure required horizontal space
    //     const textWidth = ceilPixel(ctx.measureText(longestYunit).width);
    //     const yIncrement = floorPixel((height - 2 * p) / (n-1));

    //     let y = startY;
    //     for (let i=0; i < n; i++) {
    //         // y axis legend text
    //         ctx.fillText(yUnits[i], textWidth, height - y);

    //         // dashed line with aplha value
    //         ctx.globalAlpha = 0.7;

    //         ctx.beginPath();
    //         // little shift (g/2) to the right for the xAxis legend
    //         ctx.moveTo(textWidth + g - g/2, height - y);
    //         ctx.lineTo(width - p + g/2, height - y);
    //         ctx.stroke();
    //         ctx.globalAlpha = 1;

    //         y += yIncrement;
    //     }

    //     endY = y - yIncrement;

    //     return [textWidth + g, startY, endY];
    // }

    function calculateCubicBezier(x1, y1, x2, y2) {
        // calculate midpoint
        sX = (x2 - x1) / 2; // always positive as x1 is before x2 on graph
        sY = (y2 - y1) / 2;   // can be negative if y2 is higher than y1 

        // ratios determining shape of cubic beziér
        const xRatio = 2/3;
        const yRatio = 1/3;

        // control points
        cp1x = x1 + xRatio * sX;
        cp1y = y1 + yRatio * sY;

        cp2x = x2 - xRatio * sX;
        cp2y = y2 - xRatio * sY;

        // return canva's cubic bezier arguments format
        return [cp1x, cp1y, cp2x, cp2y, x2, y2];
    }

    // function renderX(ctx, startX, startY, endY, width, height, radius, p, g) {
    //     ctx.textBaseline = "ideographic";
    //     ctx.textAlign = "center";
    //     // line width to the nearest half pixel value (prevents blurry lines)
    //     ctx.lineWidth = radius/2;
    //     ctx.strokeStyle = lineColor;
    //     ctx.setLineDash([]);
    //     ctx.beginPath();

    //     var y = NaN;
    //     const n = xUnits.length;
    //     let x = startX;
    //     const xIncrement = floorPixel((width - startX - p) / (n-1));

    //     for (let i=0; i < n; i++) {
    //         // calculate relative pos on vertical axis
    //         let relativePos = graphValues[i] / upperBound;
    //         let oldY = y;
    //         y = startY + relativePos * (endY - startY);

    //         // previous line end (bezier curve)
    //         // ctx.bezierCurveTo(calculateCubicBezier(x - xIncrement, oldY, x, y));
    //         ctx.lineTo(x, height - y);
    //         ctx.stroke();

    //         // fill space under line
    //         ctx.globalAlpha = 0.15;
    //         ctx.lineTo(x, height - startY);
    //         ctx.lineTo(x - xIncrement, height - startY);
    //         ctx.fill();
    //         ctx.globalAlpha = 1;

    //         // legend text
    //         ctx.beginPath();
    //         ctx.fillStyle = legendColor;
    //         ctx.fillText(xUnits[i], x, height - p + floorPixel(p));
    //         // data intersection dot
    //         ctx.fillStyle = lineColor;
    //         ctx.arc(x, height - y, radius, 0, Math.PI * 2);
    //         ctx.fill();

    //         // next line beggining
    //         ctx.beginPath();
    //         ctx.moveTo(x, height - y);

    //         x += xIncrement;
    //     }
    // }
     
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
        ctx.lineWidth = mainLineWidth;
        ctx.strokeStyle = lineColor;
        ctx.setLineDash([]);
        ctx.beginPath();

        y = NaN;
        let x = textWidth + g;
        const xIncrement = floorPixel((width - x - p) / (units["x"].length-1));

        for (let i=0; i < units["x"].length; i++) {
            // calculate relative pos on vertical axis
            let relativePos = values[i] / upperBound;
            let oldY = y;
            y = startY + relativePos * (endY - startY);

            // previous line end (bezier curve)
            ctx.bezierCurveTo(...calculateCubicBezier(x - xIncrement, oldY, x, height - y));
            // ctx.lineTo(x, height - y);
            ctx.stroke();

            // fill space under line
            ctx.globalAlpha = 0.15;
            ctx.lineTo(x, height - startY);
            ctx.lineTo(x - xIncrement, height - startY);
            ctx.fill();
            ctx.globalAlpha = 1;

            // legend text
            ctx.beginPath();
            ctx.fillStyle = legendColor;
            ctx.fillText(units["x"][i], x, height - p + floorPixel(p));
            // data intersection dot
            ctx.fillStyle = lineColor;
            ctx.arc(x, height - y, radius, 0, Math.PI * 2);
            ctx.fill();

            // next line beggining
            ctx.beginPath();
            ctx.moveTo(x, height - y);

            x += xIncrement;
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
