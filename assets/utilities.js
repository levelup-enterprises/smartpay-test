$(() => {
	//# Handle slide effect when formData is available
	setTimeout(() => {
		$(".hide-left").addClass("slide-out");
		$(".hide-right").addClass("slide-in");
	}, 100);

	//# Change title on qualification
	if ($("section.hide-left").length > 0) {
		$("title").html("Your qualified!");
	}

	//# prevent decimals
	$("input[type=number]").keydown(function (e) {
		var kCode = e.which || e.keyCode;
		if (kCode == 190 || kCode == 110) return false;
		if (e.which === 86 && (e.ctrlKey || e.metaKey)) return false;
	});

	//*----------------------------------------------------
	//# Post to email to generate pdf and email it
	$("form[name=email_pdf]").submit(function (e) {
		e.preventDefault();

		const url = "/email";
		const form = $(this).serializeArray();
		console.log(form);

		$.post(url, form).done((res) => {
			console.log(res);
			if (res.success) {
				$("popup .content").html(res.success);
				$(".popup").addClass("slide-in");
				setTimeout(() => {
					$(".popup").removeClass("slide-in");
				}, 4000);
			}
		});
	});
	//*----------------------------------------------------

	//# Handle close popup
	$(".popup .close").click(() => {
		$(".popup").removeClass("slide-in");
	});
});
