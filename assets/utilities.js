$(() => {
	$(".fade-up").slideUp(() => {
		500;
		setTimeout(() => {
			$(".fade-down").slideDown(500);
		}, 100);
	});
});
