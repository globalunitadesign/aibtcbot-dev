<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href=" {{ asset('images/logos/emblem.png') }}" size="32x32">
    <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        .orgchart-container {
            overflow: hidden;
            width: 100%;
            height: 100vh;
            max-width: 100%;
            margin: 0 auto;
        }

        .orgchart-node {
            background-color: #fff;
            border-radius: 30px;
            padding: 15px;
            width: 200px;
            height: auto;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            font-family: 'Arial', sans-serif;
            color: #333;
            text-align: center;
        }

        .orgchart-node .google-visualization-orgchart-node-name {
            font-size: 16px;
            font-weight: bold;
        }

        .orgchart-node .google-visualization-orgchart-node-tooltip {
            font-size: 14px;
            color: #555;
        }

        .google-visualization-orgchart-lineleft {
            border-left: 1px solid #000 !important;
        }

        .google-visualization-orgchart-lineright {
            border-right: 1px solid #000 !important;
        }

        .google-visualization-orgchart-linebottom {
            border-bottom: 1px solid #000 !important;
        }


        .google-visualization-orgchart-tooltip {
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 10px;
            font-size: 13px;
            max-width: 250px;
        }

        #chart_div {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
    </style>

    <title>{{ config('app.name', 'Laravel') }}</title>

</head>
<body>

<main class="container-fluid px-0 vh-100 position-relative">
    <div class="position-fixed top-2rem end-2rem z-1">
        <a href="{{ url()->previous() }}">
            <button type="button" class="btn-close rounded-circle bg-light p-3 opacity-75"></button>
        </a>
    </div>
    @if(request()->has('admin'))
    <div class="card border-0 p-3 m-0">
        <div class="card-body p-0">
            <form action="" method="GET">
                <input type="hidden" name="admin" value="1">
                <div class="row align-items-center">
                    <div class="col-8 col-md-4">
                        <input type="text" name="search" id="search" class="form-control" placeholder="id" value="{{ request()->get('search') }}">
                    </div>
                    <div class="col-4 col-md-2 text-start">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card border-0">
        <div class="card-body p-0">
            <div class="orgchart-container" class="h-auto">
                <div class="chart-div" id="chart_div"></div>
            </div>
        </div>
    </div>
    @else
    <div class="card border-0">
        <div class="card-body p-0">
            <div class="orgchart-container h-auto">
                <div class="chart-div" id="chart_div"></div>
            </div>
        </div>
    </div>
    @endif
</main>

<script src="{{ asset('libs/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<script type="text/javascript">

function drawChart() {
    const data = new google.visualization.DataTable();
    data.addColumn('string', 'Name');
    data.addColumn('string', 'Manager');
    data.addColumn('string', 'ToolTip');

    const treeData = @json($chartData);

    const rows = treeData.map(function(node) {
        let id = node.id;
        let parent = node.parent || '';
        let info = node.info || '';

        return [{
            'v': id,
            'f': "<div class='text-muted text-center' style='width:200px;'>" + info + "</div>"
        }, parent, info];
    });

    data.addRows(rows);

    const chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
    chart.draw(data, {
        'allowHtml': true,
        'size': 'large',
        'nodeClass': 'orgchart-node',
        'tooltip': {isHtml: true},
        'backgroundColor': 'transparent',
        'edges': {
            'color': '#000',
            'width': 2
        }
    });
}

let isDragging = false;
let startX, startY;
let offsetX = 0, offsetY = 0;
let scale = 1;
let initialDistance = 0;


const PC_MIN_SCALE = 0.5;
const PC_MAX_SCALE = 3;
const PC_ZOOM_SENSITIVITY = 0.05;
const MOBILE_MIN_SCALE = 0.05;
const MOBILE_MAX_SCALE = 3;
const MOBILE_ZOOM_SENSITIVITY = 0.005;


document.addEventListener('DOMContentLoaded', () => {
    google.charts.load('current', {packages: ["orgchart"]});
    google.charts.setOnLoadCallback(drawChart);

    const zoomContainer = document.querySelector('.orgchart-container');
    const zoomContent = document.querySelector('.chart-div');

    if (!zoomContainer || !zoomContent) {
        console.error('zoomContainer 또는 zoomContent 요소를 찾을 수 없습니다.');
        return;
    }

    zoomContainer.addEventListener('wheel', (event) => {
        const zoomChange = event.deltaY < 0 ? PC_ZOOM_SENSITIVITY : -PC_ZOOM_SENSITIVITY;
        scale += zoomChange;


        scale = Math.max(PC_MIN_SCALE, Math.min(PC_MAX_SCALE, scale));

        const mouseX = event.offsetX / zoomContainer.offsetWidth * 100;
        const mouseY = event.offsetY / zoomContainer.offsetHeight * 100;
        zoomContent.style.transformOrigin = `${mouseX}% ${mouseY}%`;

        zoomContent.style.transform = `scale(${scale}) translate(${offsetX}px, ${offsetY}px)`;
        event.preventDefault();
    });

    zoomContent.addEventListener('mousedown', (event) => {
        isDragging = true;
        startX = event.clientX - offsetX;
        startY = event.clientY - offsetY;
        zoomContent.style.cursor = 'grabbing';
    });

    zoomContent.addEventListener('mousemove', (event) => {
        if (isDragging) {
            const moveX = event.clientX - startX;
            const moveY = event.clientY - startY;
            offsetX = moveX;
            offsetY = moveY;

            zoomContent.style.transform = `scale(${scale}) translate(${offsetX}px, ${offsetY}px)`;
        }
    });

    zoomContent.addEventListener('mouseup', () => {
        isDragging = false;
        zoomContent.style.cursor = 'grab';
    });

    zoomContent.addEventListener('mouseleave', () => {
        isDragging = false;
        zoomContent.style.cursor = 'grab';
    });

    zoomContent.addEventListener('touchstart', (event) => {
        if (event.touches.length === 2) {

            initialDistance = getDistance(event.touches);
        } else if (event.touches.length === 1) {
            isDragging = true;
            const touch = event.touches[0];
            startX = touch.clientX - offsetX;
            startY = touch.clientY - offsetY;
        }
    });

    zoomContent.addEventListener('touchmove', (event) => {
        if (event.touches.length === 2) {

            const currentDistance = getDistance(event.touches);
            const zoomFactor = (currentDistance - initialDistance) * MOBILE_ZOOM_SENSITIVITY;
            initialDistance = currentDistance;

            scale += zoomFactor;

            scale = Math.max(MOBILE_MIN_SCALE, Math.min(MOBILE_MAX_SCALE, scale));

            const centerX = (event.touches[0].clientX + event.touches[1].clientX) / 2 / zoomContainer.offsetWidth * 100;
            const centerY = (event.touches[0].clientY + event.touches[1].clientY) / 2 / zoomContainer.offsetHeight * 100;
            zoomContent.style.transformOrigin = `${centerX}% ${centerY}%`;

            zoomContent.style.transform = `scale(${scale}) translate(${offsetX}px, ${offsetY}px)`;
            event.preventDefault();
        } else if (isDragging && event.touches.length === 1) {

            const touch = event.touches[0];
            const moveX = touch.clientX - startX;
            const moveY = touch.clientY - startY;
            offsetX = moveX;
            offsetY = moveY;

            zoomContent.style.transform = `scale(${scale}) translate(${offsetX}px, ${offsetY}px)`;
            event.preventDefault();
        }
    });

    zoomContent.addEventListener('touchend', (event) => {
        if (event.touches.length < 2) {
            isDragging = false;
        }
    });


    function getDistance(touches) {
        const dx = touches[0].clientX - touches[1].clientX;
        const dy = touches[0].clientY - touches[1].clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }
});


</script>

</body>
</html>
