// chart utility functions
var randomColorFactor = function() {
    return Math.round(Math.random() * 255);
};
var randomColor = function() {
    return 'rgba(' + randomColorFactor() + ',' + randomColorFactor() + ',' + randomColorFactor() + ',.7)';
};
function range(start, end) {
    var foo = [];
    for (var i = start; i <= end; i++) {
        foo.push(i);
    }
    return foo;
};

//  Creates dataset for chart, based on csv label id
function dataSetByLabelId(labelId) {
    return {
        type: 'horizontalBar',
        label: csvLabels[labelId],
        backgroundColor: randomColor(),
        data: csvData[labelId],
        borderColor: 'white',
        borderWidth: 2,
        csvLabelId: labelId
    }
};

// Adds selected columns to chart
function addColumns() {
    var moveFrom = document.getElementById("availableCols");
    var moveTo = document.getElementById("selectedCols");
    while (moveFrom.selectedIndex > -1) {
        var labelId = moveFrom[moveFrom.selectedIndex].value;
        window.myBarChart.data.datasets.push(dataSetByLabelId(labelId));
        moveTo.appendChild(moveFrom[moveFrom.selectedIndex]);
    }
    window.myBarChart.update();
};
// Removes selected columns from chart
function removeColumns() {
    var moveFrom = document.getElementById("selectedCols");
    var moveTo = document.getElementById("availableCols");
    while (moveFrom.selectedIndex > -1) {
        var labelId = moveFrom[moveFrom.selectedIndex].value;
        for (var i = 0; i < window.myBarChart.data.datasets.length; i++) {
            if (window.myBarChart.data.datasets[i].csvLabelId == labelId) {
                window.myBarChart.data.datasets.splice(i, 1);
            }
        }
        moveTo.appendChild(moveFrom[moveFrom.selectedIndex]);
    }
    window.myBarChart.update();
};

function buildGraph(csvDataset) {
    // Rows to show:
    var demoRowCount = 10;

    // First row is labels
    window.csvLabels = csvDataset.substr(0, csvDataset.indexOf("\n")).split(",");
    // Other rows are values
    var csvDataset = csvDataset.substr(csvDataset.indexOf("\n") + 1);
    var csvLinesStrings = csvDataset.split("\n");
    var csvLines = [];
    csvLinesStrings.forEach(function (lineString, linestrIndex) {
        var lineArray = lineString.split(",");
        csvLines.push(lineArray);
    });
    // Stores values in a 2d array
    window.csvData = new Array(csvLabels.length);
    for (var i = 0; i < csvLabels.length; i++) {
        csvData[i] = new Array();
    }
    csvLabels.forEach(function (csvLabel, labelIndex) {
        for (var lineIndex = 0; lineIndex < demoRowCount; lineIndex++) {
            csvData[labelIndex].push(csvLines[lineIndex][labelIndex]);
        }
    });


    // Fills column editor boxes:
    csvLabels.forEach(function (csvLabel, labelIndex) {
        var option = document.createElement("option");
        option.text = csvLabel;
        option.value = labelIndex;
        document.getElementById("availableCols").add(option);
    });


    // Adds a sample dataset to chart and set abscissa to 1..demoRowCount
    var sampleId = 0;
    var barChartData = {
        labels: range(1, demoRowCount),
        datasets: [dataSetByLabelId(sampleId)]
    };
    // Moves dataset's column to the "selected" box
    var avCols = document.getElementById("availableCols");
    var selCols = document.getElementById("selectedCols");
    for(var i=0; i < avCols.options.length; i++)
    {
        if(avCols.options[i].value == sampleId) {
            selCols.appendChild(avCols.options[i]);
            break;
        }
    }

    // Creates the chart
    var ctx = document.getElementById("canvas").getContext("2d");
    window.myBarChart = new Chart(ctx, {
        type: 'horizontalBar',
        data: barChartData,
        options: {
            responsive: true,
            title: {
                display: true,
                text: $("#chartArea").data('name')
            },
            animation: {
                onComplete: function () {
                    var chartInstance = this.chart;
                    var ctx = chartInstance.ctx;
                    ctx.textAlign = "left";
                    /*Chart.helpers.each(this.data.datasets.forEach(function (dataset, i) {
                     var meta = chartInstance.controller.getDatasetMeta(i);
                     Chart.helpers.each(meta.data.forEach(function (bar, index) {
                     ctx.fillText(dataset.data[index], bar._model.x, bar._model.y - 10);
                     }),this)
                     }),this);*/
                }
            },
            scales: {
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Row'
                    }
                }]
            }
        }
    });
}

// AJAX call to get csv document
$(document).ready(function() {
    // Load data
    var document_id = $("#chartArea").data('id');
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '/document/demo/id/' + document_id);
    xhr.onload = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            buildGraph(xhr.responseText);
        }
    };
    xhr.send(null);
});
