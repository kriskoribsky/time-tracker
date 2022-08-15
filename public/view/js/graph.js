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

    function formatDayName(day) {
        const days = {
            "Mon": "Monday",
            "Tue": "Tuesday",
            "Wed": "Wednesday",
            "Thu": "Thursday",
            "Fri": "Friday",
            "Sat": "Saturday",
            "Sun": "Sunday"
        }

        return days[day];
    }

    function formatSeconds(seconds) {
        var h = Math.floor(seconds / 3600);
        var m = Math.floor(seconds % 3600 / 60);

        var hoursDisplay = h > 0 ? h + (h === 1 ? " hour" : " hours") : "";
        var minutesDisplay = m > 0 ? m + (m === 1 ? " minute" : " minutes") : "";

        return hoursDisplay + " " + minutesDisplay;
    }

    // chart data retrieving & manipulation
    // ==========================================================================
    const canvas = document.getElementById("work-time-graph");
    let ctx;
    const parsed_data = JSON.parse(canvas.getAttribute("data-graph-data"));


    let maxValue;
    const minutesPostfix = 'min';
    const hoursPostfix = 'h';

    let units = {};
    let values = {};

    values.x = Object.values(parsed_data).reverse();
    // reverse data object since the rendering will start from the oldest day
    units.x = Object.keys(parsed_data).reverse();
    [units.y, maxValue] = getYunits(values.x);

    const longestYunit = (() => {
        max = units.y[0];

        for (let i=1; i < units.y.length; i++) {
            if (units.y[i].length > max.length) {
                max = units.y[i];
            }
        }
        return max;
    })();

    console.log("xUnits:", units.x);
    console.log("values:", values.x);
    console.log("yUnits:", units.y);

    // get graph colors (which depend on project group selected in landing page)
    const bodyStyles = window.getComputedStyle(document.body);
    const columnPrimaryClr = bodyStyles.getPropertyValue("--primary-color");
    const columnGradientClr = bodyStyles.getPropertyValue("--primary-gradient-secondary-color");
    const legendColor = bodyStyles.getPropertyValue("--section-text-color");
    const legendFont = "Roboto, -apple-system, sans-serif";



    // Static canvas objects sizes
    // ==========================================================================
    // 5% padding between graph objects
    const internalPadding = 0.05;
    // pixel count increase on-canvas to improve crispiness
    const res = 2;
    // how fast do columns move (px per second) in animation (rise from ground up effect)
    const colAnimationSpeed = 12;
    // animation only on page reload
    let animationDone = false;
    
    // gap between graph columns (higher = narrower columns)
    const colGapRatio = 1.6;



    // Dynamic canvas objects sizes
    // ==========================================================================
    let p; // padding
    let g; // gap
    let width;
    let height;

    let ratio;
    // graph starting points
    let origin = {};

    // required horizontal space for legend on Y axis
    let maxTextWidth;

    // gap between graph columns
    let colGap;

    // maximal graph value height on canvas
    let ceiling;
    let colWidth;

    // path2d object of graph columns for displaying tooltip canvas on hover
    let columns = [];


    // Hover tooltip
    // ==========================================================================
    var over;
    const tooltip = document.getElementById("work-time-tooltip");
    const tooltipDaySpan = document.getElementById("tooltip-day");
    const tooltipDataSpan = document.getElementById("tooltip-data");
    
    const cssTransformTransitionClass = "transform-tooltip-transition";
    const classShowTooltipBefore = "tooltip-show-before";
    const classShowTooltipAfter = "tooltip-show-after";

    let firstTooltip = true;
    const tooltipOpacity = 0.7;

    canvas.addEventListener("mousemove", event => {
        // console.log(event.clientX, event.clientY);
        // console.log(event.offsetX, event.offsetY);

        // loop trough columns objects and check for hover
        var over = false;
        for (let i = 0; i < columns.length; i++) {
            if (ctx.isPointInPath(columns[i].path, event.offsetX * ratio, event.offsetY * ratio)) {
                if (!columns[i].hovering) {

                    // hover opacity change
                    ctx.globalAlpha = 0.8;
                    ctx.clearRect(...columns[i].rect);
                    ctx.fill(columns[i].path);
                    ctx.globalAlpha = 0.6;

                    // tooltip data
                    tooltipDaySpan.textContent = formatDayName(columns[i].day);
                    tooltipDataSpan.textContent = formatSeconds(columns[i].data);


                    // tooltip pos
                    var xTranslate, yTranslate;

                    if (columns[i].fromLeft) {
                        xTranslate = columns[i].point.left + 5;
                        tooltip.classList.add(classShowTooltipBefore);
                        tooltip.classList.remove(classShowTooltipAfter);
                    } else {
                        xTranslate = columns[i].point.left - tooltip.offsetWidth - 5;
                        tooltip.classList.add(classShowTooltipAfter);
                        tooltip.classList.remove(classShowTooltipBefore);
                    }

                    yTranslate = columns[i].point.top - tooltip.offsetHeight/2 + 2;
                    tooltip.style.transform = "translate(" + xTranslate + "px, " + yTranslate + "px)";

                    columns[i].hovering = true;

                    if (firstTooltip) {
                        // add transition animation after first transform
                        setTimeout(() => {
                            tooltip.classList.add(cssTransformTransitionClass);
                        }, 300)
                        firstTooltip = false;
                    }
                }

            over = true;

            } else {
                columns[i].hovering = false;
                ctx.clearRect(...columns[i].rect);
                ctx.fill(columns[i].path);
            }

        if (over) {
            canvas.style.cursor = "pointer";
            tooltip.style.opacity = tooltipOpacity;
        } else {
            canvas.style.cursor = "default";
            tooltip.style.opacity = 0;
        }
    }
})


    // canvas rendering
    // ==========================================================================
    // round down to nearest pixel to prevent blurry look
    function floorPixel(pixels) {
        return Math.floor(pixels);
    }

    function ceilPixel(pixels) {
        return Math.ceil(pixels);
    } 

    function halfPixel(pixels) {
        return Math.floor(pixels) + 0.5;
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

    function animateColumns(cols) {
        // animation on page reload else just render columns without animation
        if (!animationDone) {
            let animationFinished = true;

            // raise col's height by predefined animation speed & request new animation frame
            for (let i = 0; i < cols.length; i++) {

                if (cols[i].y < cols[i].ceil) {
                    animationFinished = false;

                    if (cols[i].y + colAnimationSpeed > cols[i].ceil) {
                        cols[i].y = cols[i].ceil;
                    } else {
                        cols[i].y += colAnimationSpeed;
                    }
                    ctx.clearRect(cols[i].x, height - cols[i].y, colWidth, cols[i].y - origin.y);
                    ctx.fillRect(cols[i].x, height - cols[i].y, colWidth, cols[i].y - origin.y);
                }
            }

            if (!animationFinished) {
                window.requestAnimationFrame(() => {animateColumns(cols)});
            } else {
                window.requestAnimationFrame(() => {animateColumns(cols)});
                animationDone = true;
            }
        } else {
            // after animation is done, clear rectangle and instead replace them with path2d objects
            // for isPointInPath() checking on hovering them
            // clear last column objects
            columns = [];
            
            for (let i = 0; i < cols.length; i++) {
                // column object construction
                let column = {};
                column.data = values.x[i];
                column.day = units.x[i];

                let point = {};
                // devide by ratio go get canvas display pixels instead of redner ones
                point.left = (cols[i].x + colWidth / 2) / ratio;
                point.right = (width - cols[i].x - colWidth / 2) / ratio;
                point.top = (height - cols[i].ceil) / ratio;
                column.point = point;

                column.fromLeft = !Boolean(Math.round(i / (cols.length-1)));
                column.rect = [cols[i].x, height - cols[i].ceil, colWidth, cols[i].ceil - origin.y];
                column.hovering = false;

                var path = new Path2D();
                path.rect(cols[i].x, height - cols[i].ceil, colWidth, cols[i].ceil - origin.y);

                ctx.clearRect(...column.rect);
                ctx.fill(path);

                column.path = path;
                columns.push(column);
            }

            console.log(columns);
        }
    }

    function renderYaxis() {
        // save previous context settings & restore them after function
        ctx.save();

        ctx.strokeStyle = legendColor;
        ctx.fillStyle = legendColor;
        // horizontal lines
        ctx.lineWidth = 1;

        // legend text
        ctx.textBaseline = "middle";
        ctx.textAlign = "end";

        let y = p;
        const yIncrement = floorPixel((height - 2 * p) / (units.y.length-1));

        for (let i=0; i < units.y.length; i++) {
            // y axis legend text
            ctx.fillText(units.y[i], maxTextWidth, height - y);

            // dashed line with aplha value
            ctx.globalAlpha = 0.3;

            ctx.beginPath();
            // little shift (g) to the right for the xAxis legend
            ctx.moveTo(halfPixel(origin.x - g/2), halfPixel(height - y));
            ctx.lineTo(halfPixel(width - p + g/2), halfPixel(height - y));
            ctx.stroke();
            ctx.globalAlpha = 1;

            y += yIncrement;
        }
        ctx.restore();

        // return ceiling
        return y - yIncrement;
    }

    function renderXaxis() {
        // save previous context settings & restore them after function
        ctx.save();

        ctx.textBaseline = "ideographic";
        ctx.textAlign = "center";

        let cols = [];
        // col width = (available width - (n-1) * colGap space) / n
        colWidth = floorPixel(((width - origin.x - p) - (colGap * (units.x.length - 1))) / units.x.length);

        let x = origin.x;
        for (let i=0; i < units.x.length; i++) {

            let col = {};
            col.x = x;
            // all culumns will intially start at the bottom (animation)
            col.y = origin.y;
            //column final y value (col ceiling)
            col.ceil = origin.y + (values.x[i] / maxValue) * (ceiling - origin.y);
            cols.push(col);

            // legend text
            ctx.beginPath();
            ctx.fillStyle = legendColor;
            ctx.fillText(units.x[i], x + colWidth / 2, height);

            x += colWidth + colGap;
        }

        ctx.restore();
        return cols;
    }

    function renderGraph() {

        ctx = canvas.getContext("2d");

        // Sizes
        // ==========================================================================
        p = ceilPixel(internalPadding * Math.max(width, height));   // padding
        g = ceilPixel(p / 2);                                       // gap
        ctx.font = (12 * ratio) + "px " + legendFont;
    
        // graph columns
        colGap = colGapRatio * p;

        // measure required horizontal space for hours
        maxTextWidth = ceilPixel(ctx.measureText(longestYunit).width);

        origin.y = p;
        origin.x =  maxTextWidth + g;

        // Render Y axis
        // ==========================================================================
        ceiling = renderYaxis();

        // Render X axis
        // ==========================================================================
        cols = renderXaxis();

        // Animate main graph columns
        // ==========================================================================
        ctx.fillStyle = columnPrimaryClr;
        ctx.globalAlpha = 0.6;
        
        window.requestAnimationFrame(() => {animateColumns(cols)});
    }

    function updateCanvasSize() {
        // keep the canvas rendering crisp even on higher pixel ratio (e.g. when zooming)
        ratio = window.devicePixelRatio * res;

        width = canvas.offsetWidth * ratio;
        height = canvas.offsetHeight * ratio;

        canvas.width = width;
        canvas.height = height;

        renderGraph();
    }
    // update canvas everytime the parent size monitor div changes size
    const sizeMonitorDiv = canvas.parentElement;
    new ResizeObserver(updateCanvasSize).observe(sizeMonitorDiv);
}
