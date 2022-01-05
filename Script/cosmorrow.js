/********  Deroulants  *********
    var btn = document.querySelector("input");
    var clic = "unclicked";

    btn.addEventListener("click", updateBtn);

    function updateBtn() 
    {
            if (btn.value === "clicked") 
            {
            btn.value = "unclicked";
            clic = "unclicked";
            } else 
            {
            btn.value = "clicked";
            clic = "clicked";
            }
    }

    sfHover = function() {

            var sfEls = document.getElementById("menuusers").getElementsByTagName("LI");

            for (var i=0; i<sfEls.length; i++) {

                    sfEls[i].onmouseover=function() {

                            this.className+=" sfhover";

                    }

                    sfEls[i].onmouseout=function() {

                            this.className=this.className.replace(new RegExp(" sfhover\\b"), "");

                    }

            }

    }

    if (window.attachEvent) window.attachEvent("onload", sfHover);
*/



/********  Carousel  *********/
jQuery(document).ready(function()
{
    //tableau des <p class=pChange> dans <div id=carousel>
    var paragraphe = $('#carousel .pChange');
    //indice du paragraphe en cours de selection 
    var ind;
    if(ind == null)
    {
        ind=0;
    }
    //alert('indice = '+ind+'/'+paragraphe.length);
    
    function changeImg()
    {
        for(i = 0; i < paragraphe.length; i++)
        {
            if(ind == i)
            {
                $('#changingP'+i).css('display', 'block');
                $('#changingP'+i).css('visibility', 'visible');
            }
            else
            {
                $('#changingP'+i).css('display', 'none');
                $('#changingP'+i).css('visibility', 'hidden');
            }
            //alert($('#changingImg'+i).attr('src') +'=='+ $('#changingP'+ind).css('display'));
        }
    }

    //events clic next ou prev
    $('#previous').click(
    function()
    {
        if(ind == 0)
        {
            ind = paragraphe.length;
        }
        else
        {
            ind -= 1;
        }
        changeImg();
        //alert($('#changingImg'+ind).attr('src'));
    });
    $('#next').click(
    function()
    {
        if(ind == paragraphe.length)
        {
            ind = 0;
        }
        else
        {
            ind += 1;
        }
        changeImg();
        //alert($('#changingImg'+ind).attr('src'));
    });

});



var ctx = document.getElementById('graph').getContext('2d');
//var fillPattern = ctx.createPattern(img, 'repeat');

var gradientStroke = ctx.createLinearGradient(0, 0, 800, 0);
gradientStroke.addColorStop(0.1, 'rgba(0, 0, 0, 1)');
gradientStroke.addColorStop(0.2, 'rgba(200, 0, 200, 1)'); 
gradientStroke.addColorStop(0.33, 'rgba(0, 0, 255, 1)');
gradientStroke.addColorStop(0.43, 'rgba(0, 255, 255, 1)');
gradientStroke.addColorStop(0.53, 'rgba(0, 255, 0, 1)');
gradientStroke.addColorStop(0.63, 'rgba(255, 255, 0, 1)');
gradientStroke.addColorStop(0.73, 'rgba(200, 100, 0, 1)');
gradientStroke.addColorStop(0.83, 'rgba(255, 0, 0, 1)');
gradientStroke.addColorStop(0.9, 'rgba(150, 0, 0, 1)');
gradientStroke.addColorStop(0.99, 'rgba(0, 0, 0, 1)');

var chart = new Chart(ctx, {
    type: 'line',
    data: 
    {
        labels: [".$list1."],
        datasets: 
        [{
            label: 'Spectrum rendering',
            data: [".$list2."],
            borderColor: 'rgb(200, 0, 50)',
            borderWidth: 1,
            backgroundColor: gradientStroke
        }]  
    },
    options: 
    {
        ticks: {
            reverse: true
        }
    }
}); 

document.write('<img src="'+image+'"/>');
var image = ctx.toDataURL("image/png");
chart.render();	
chart.exportChart({format: 'png'});
document.getElementById('saveBtn').addEventListener('click',function(){
    chart.exportChart({format: 'png'});
}); 