var ComponentsNoUiSliders = function() {

    var demo2 = function() {
        var connectSlider = document.getElementById('demo2');

        noUiSlider.create(connectSlider, {
            start: [20],
            connect: false,
            range: {
                'min': 0,
                'max': 100
            }
        });
    }

    var demo3 = function() {
        var connectSlider = document.getElementById('demo3');

        noUiSlider.create(connectSlider, {
            start: [20, 80],
            connect: false,
            range: {
                'min': 0,
                'max': 100
            }
        });

        var connectBar = document.createElement('div'),
            connectBase = connectSlider.getElementsByClassName('noUi-base')[0],
            connectHandles = connectSlider.getElementsByClassName('noUi-origin');

        // Give the bar a class for styling and add it to the slider.
        connectBar.className += 'connect';
        connectBase.appendChild(connectBar);

        connectSlider.noUiSlider.on('update', function( values, handle ) {

            // Pick left for the first handle, right for the second.
            var side = handle ? 'right' : 'left',
            // Get the handle position and trim the '%' sign.
                offset = (connectHandles[handle].style.left).slice(0, - 1);

            // Right offset is 100% - left offset
            if ( handle === 1 ) {
                offset = 100 - offset;
            }

            connectBar.style[side] = offset + '%';
        });
    }

    var demo4 = function() {
        //** init the select
        var select = document.getElementById('demo4_select');

        // Append the option elements
        for ( var i = -20; i <= 40; i++ ) {
            var option = document.createElement("option");
                option.text = i;
                option.value = i;
            select.appendChild(option);
        }

        //** init the slider
        var html5Slider = document.getElementById('demo4');

        noUiSlider.create(html5Slider, {
            start: [ 10, 30 ],
            connect: true,
            range: {
                'min': -20,
                'max': 40
            }
        });

        //** init the input
        var inputNumber = document.getElementById('demo4_input');

        html5Slider.noUiSlider.on('update', function( values, handle ) {

            var value = values[handle];

            if ( handle ) {
                inputNumber.value = value;
            } else {
                select.value = Math.round(value);
            }
        });

        select.addEventListener('change', function(){
            html5Slider.noUiSlider.set([this.value, null]);
        });

        inputNumber.addEventListener('change', function(){
            html5Slider.noUiSlider.set([null, this.value]);
        });
    }

    var demo5 = function() {
        var nonLinearSlider = document.getElementById('demo5');

        noUiSlider.create(nonLinearSlider, {
            connect: true,
            behaviour: 'tap',
            start: [ 500, 4000 ],
            range: {
                // Starting at 500, step the value by 500,
                // until 4000 is reached. From there, step by 1000.
                'min': [ 0 ],
                '10%': [ 500, 500 ],
                '50%': [ 4000, 1000 ],
                'max': [ 10000 ]
            }
        });

        // Write the CSS 'left' value to a span.
        function leftValue ( handle ) {
            return handle.parentElement.style.left;
        }

        var lowerValue = document.getElementById('demo5_lower-value'),
            upperValue = document.getElementById('demo5_upper-value'),
            handles = nonLinearSlider.getElementsByClassName('noUi-handle');

        // Display the slider value and how far the handle moved
        // from the left edge of the slider.
        nonLinearSlider.noUiSlider.on('update', function ( values, handle ) {
            if ( !handle ) {
                lowerValue.innerHTML = values[handle] + ', ' + leftValue(handles[handle]);
            } else {
                upperValue.innerHTML = values[handle] + ', ' + leftValue(handles[handle]);
            }
        });
    }

    var demo6 = function() {
        // Store the locked state and slider values.
        var lockedState = false,
            lockedSlider = false,
            lockedValues = [60, 80],
            slider1 = document.getElementById('demo6_slider1'),
            slider2 = document.getElementById('demo6_slider2'),
            lockButton = document.getElementById('demo6_lockbutton'),
            slider1Value = document.getElementById('demo6_slider1-span'),
            slider2Value = document.getElementById('demo6_slider2-span');

        // When the button is clicked, the locked
        // state is inverted.
        lockButton.addEventListener('click', function(){
            lockedState = !lockedState;
            this.textContent = lockedState ? 'unlock' : 'lock';
        });

        function crossUpdate ( value, slider ) {

            // If the sliders aren't interlocked, don't
            // cross-update.
            if ( !lockedState ) return;

            // Select whether to increase or decrease
            // the other slider value.
            var a = slider1 === slider ? 0 : 1, b = a ? 0 : 1;

            // Offset the slider value.
            value -= lockedValues[b] - lockedValues[a];

            // Set the value
            slider.noUiSlider.set(value);
        }

        noUiSlider.create(slider1, {
            start: 60,

            // Disable animation on value-setting,
            // so the sliders respond immediately.
            animate: false,
            range: {
                min: 50,
                max: 100
            }
        });

        noUiSlider.create(slider2, {
            start: 80,
            animate: false,
            range: {
                min: 50,
                max: 100
            }
        });

        slider1.noUiSlider.on('update', function( values, handle ){
            slider1Value.innerHTML = values[handle];
        });

        slider2.noUiSlider.on('update', function( values, handle ){
            slider2Value.innerHTML = values[handle];
        });

        function setLockedValues ( ) {
            lockedValues = [
                Number(slider1.noUiSlider.get()),
                Number(slider2.noUiSlider.get())
            ];
        }

        slider1.noUiSlider.on('change', setLockedValues);
        slider2.noUiSlider.on('change', setLockedValues);

        // The value will be send to the other slider,
        // using a custom function as the serialization
        // method. The function uses the global 'lockedState'
        // variable to decide whether the other slider is updated.
        slider1.noUiSlider.on('slide', function( values, handle ){
            crossUpdate(values[handle], slider2);
        });

        slider2.noUiSlider.on('slide', function( values, handle ){
            crossUpdate(values[handle], slider1);
        });
    }

    var demo7 = function() {
        var softSlider = document.getElementById('demo7');

        noUiSlider.create(softSlider, {
            start: 50,
            range: {
                min: 0,
                max: 100
            },
            pips: {
                mode: 'values',
                values: [20, 80],
                density: 4
            }
        });

        softSlider.noUiSlider.on('change', function ( values, handle ) {
            if ( values[handle] < 20 ) {
                softSlider.noUiSlider.set(20);
            } else if ( values[handle] > 80 ) {
                softSlider.noUiSlider.set(80);
            }
        });
    }

    var demo8 = function() {
        var tooltipSlider = document.getElementById('demo8');

        noUiSlider.create(tooltipSlider, {
            start: [40, 50],
            connect: true,
            range: {
                'min': 30,
                '30%': 40,
                'max': 50
            }
        });

        var tipHandles = tooltipSlider.getElementsByClassName('noUi-handle'),
            tooltips = [];

        // Add divs to the slider handles.
        for ( var i = 0; i < tipHandles.length; i++ ){
            tooltips[i] = document.createElement('div');
            tipHandles[i].appendChild(tooltips[i]);
        }
  
        // Add a class for styling
        tooltips[1].className += 'noUi-tooltip';
        // Add additional markup
        tooltips[1].innerHTML = '<strong>Value: </strong><span></span>';
        // Replace the tooltip reference with the span we just added
        tooltips[1] = tooltips[1].getElementsByTagName('span')[0];

        // Add a class for styling
        tooltips[0].className += 'noUi-tooltip';
        // Add additional markup
        tooltips[0].innerHTML = '<strong>Value: </strong><span></span>';
        // Replace the tooltip reference with the span we just added
        tooltips[0] = tooltips[0].getElementsByTagName('span')[0];

        // When the slider changes, write the value to the tooltips.
        tooltipSlider.noUiSlider.on('update', function( values, handle ){
            tooltips[handle].innerHTML = values[handle];
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            demo2();
            demo3();
            demo4();
            demo5();
            demo6();
            demo7();
            demo8();
        }

    };

}();

jQuery(document).ready(function() {    
   ComponentsNoUiSliders.init(); 
});