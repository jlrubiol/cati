<script>
window.chartColors = {
    red: 'rgb(166, 22, 39)',
    orange: 'rgb(255, 142, 9)',
    yellow: 'rgb(255, 205, 86)',
    purple: 'rgb(0, 73, 140)',
    green: 'rgb(0, 107, 60)',
    blue: 'rgb(58, 152, 206)',
    grey: 'rgb(231,233,237)',
    brown: 'rgb(102, 61, 20)',
    black: 'rgb(0, 0, 0)'
};

var color = Chart.helpers.color;

var chartConfig = {
    type: 'bar',
    data: {},
    options: {
        // Elements options apply to all of the options unless overridden in a dataset
        // In this case, we are setting the border of each horizontal bar to be 2px wide
        elements: {
            rectangle: {
                borderWidth: 2,
            }
        },
        responsive: true,
        legend: {
            position: 'right',
        },
        title: {
            display: true,
            text: ''
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        },
        tooltips: {
            mode: 'index',
            intersect: true
        }
    }
};

/*
 * Adds a function to the window onload event.
 * Taken from http://blog.simonwillison.net/post/57956760515/addloadevent
 * The way this works is relatively simple:
 * if window.onload has not already been assigned a function, the function
 * passed to addLoadEvent is simply assigned to window.onload.
 * If window.onload has already been set, a brand new function is created which
 * first calls the original onload handler, then calls the new handler afterwards.
 */
function addLoadEvent(func) {
    var oldonload = window.onload;
    if (typeof window.onload != 'function') {
        window.onload = func;
    } else {
        window.onload = function() {
            if (oldonload) {
                oldonload();
            }
            func();
        }
    }
}
</script>
