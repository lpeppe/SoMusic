$(document).ready(function () {
    var keySig = document.getElementById("ks");
    Object.keys(Vex.Flow.keySignature.keySpecs).forEach(function (key) {
        var option = document.createElement("option");
        option.text = key;
        keySig.add(option);
    });
    var ren = new Renderer("score", "scoreDiv", "vmCanvas");
    document.getElementById("next").addEventListener("click", function () {
        document.getElementById("firstDiv").style.display = "none";
        document.getElementById("secondDiv").style.display = "block";
        ren.init();
    }, false);
    document.getElementById("add").addEventListener("click", function () {
        previewFloatBox.close();
        var vmData = ren.saveData();
        $('input[name=vmHidden]').val(JSON.stringify(vmData));
        var ren2 = new Renderer("feed_score", "feed_scoreDiv", "feed_vmCanvas");
        var placeholder = document.getElementById("vm_placeholder");
        placeholder.style.display = "block";
        ren2.restoreData(vmData);
    });
    document.getElementById("ks").addEventListener("change", preview, false);
    var elements = document.getElementsByName("timeLab");
    for (var i = 0; i < elements.length; i++)
        elements[i].addEventListener("click", preview, false);
    document.getElementById("ks").addEventListener("change", preview, false);
    var prevDiv = document.getElementById("prev");
    var renderer = new Vex.Flow.Renderer(prevDiv, Vex.Flow.Renderer.Backends.SVG);
    var ctx = renderer.getContext();
    renderer.resize(200, 150);
    preview();

    function preview() {
        ctx.clear();
        var prevStave = new Vex.Flow.Stave(10, 20, 200);
        if (arguments.length == 0 || arguments[0].target.tagName != "LABEL")
            var timeSign = getRadioSelected("time");
        else
            var timeSign = arguments[0].target.childNodes[1].id;
        var keySign = $("#ks :selected").text();
        prevStave.addClef("treble").addTimeSignature(timeSign).addKeySignature(keySign);
        prevStave.setContext(ctx).draw();
    }

});
