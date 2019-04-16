function create_bubble(url) {
	img = document.createElement("a");
	img.innerHTML = '+1s';
	img.href="javascript:;";
	img.style = "box-shadow:0 0 0 0.1em #84ff00 inset, 0 0 1em #4afe08 inset;display: inline-block;border-radius: 50%;width:70px;height:70px;transform-style: preserve-3d;border: 0.1px;box-sizing: content-box;text-align:center;line-height:2.2;font-size:30px;text-decoration: none;color: #3adb47;moz-user-select: -moz-none;-moz-user-select: none;-o-user-select:none;-khtml-user-select:none;-webkit-user-select:none;-ms-user-select:none;user-select:none;";
	img.ondragstart="return false;";
	img.style.position = "absolute";
	img.style.opacity = 0.9;
	img.onclick = function () {
		time += 1;
		document.getElementById("music").currentTime = 0;
		document.body.removeChild(img);
		clearInterval(thread);
		setTimeout(create_bubble);
	};
	panel = document.getElementsByClassName('panel')[0];
	pos = panel.getBoundingClientRect();
	if (Math.random() < 0.5)
		img.style.left = ((Math.random() * 0.8 + 0.1) * pos.x) + "px";
	else
		img.style.left = (pos.x * (1.9 - Math.random() * 0.8) + pos.width) + "px";
	img.style.top = (pos.y + pos.height) + "px";
	img.style['-webkit-transform'] = "scale(0.5)";
	document.body.appendChild(img);

	function update() {
		//img.x = img.getBoundingClientRect().x;
		//img.y = img.getBoundingClientRect().y;
		//img.width = img.getBoundingClientRect().width;
		img.x = parseFloat(img.style.left.substr(0, img.style.left.length - 2));
		img.y = parseFloat(img.style.top.substr(0, img.style.top.length - 2));
		img.scale = parseFloat(img.style['-webkit-transform'].substr(6, img.style['-webkit-transform'].length - 7));
		ranX = (Math.random() * 5 - 2.5) / 2;
		if (img.y < pos.y) {
			if (Math.random() > 0.5) {
				img.style.left = (img.x + ranX / 2) + "px";
			}
			img.style.top = (img.y - 2.5) + "px";
			if (img.style.opacity <= 0.02) {
				img.style.opacity = 0;
				document.body.removeChild(img);
				clearInterval(thread);
				setTimeout(create_bubble);
			} else {
				img.style.opacity -= 0.02;
			}
		} else if (img.y < pos.y + 10) {
			img.style.left = (img.x + ranX / 2) + "px";
			img.style.top = (img.y - 3) + "px";
			img.style.opacity -= 0.01;
		} else {
			img.style.left = (img.x + ranX) + "px";
			img.style.top = (img.y - 4) + "px";
		}
		if (img.scale < 1) {
			img.style['-webkit-transform'] = "scale(" + (img.scale + 0.01) + ")";
		}
	}
	thread = setInterval(update, 25);
}
