
// Todays Date
$(function () {
	var interval = setInterval(function () {
		var momentNow = moment();
		$('#today-date').html(momentNow.format('DD') + ' ' + ' '
			+ momentNow.format('- dddd').substring(0, 12));
	}, 100);
});
$(function () {
	var interval = setInterval(function () {
		var momentNow = moment();
		$('#todays-date').html(momentNow.format('DD MMMM YYYY'));
	}, 100);
});
// Loading
$(function () {
	$("#loading-wrapper").fadeOut(3000);
});
// Textarea characters left
$(function () {
	$('#characterLeft').text('140 characters left');
	$('#message').keydown(function () {
		var max = 140;
		var len = $(this).val().length;
		if (len >= max) {
			$('#characterLeft').text('You have reached the limit');
			$('#characterLeft').addClass('red');
			$('#btnSubmit').addClass('disabled');
		}
		else {
			var ch = max - len;
			$('#characterLeft').text(ch + ' characters left');
			$('#btnSubmit').removeClass('disabled');
			$('#characterLeft').removeClass('red');
		}
	});
});
// Todo list
$('.todo-body').on('click', 'li.todo-list', function () {
	$(this).toggleClass('done');
});
// Tasks
(function ($) {
	var checkList = $('.task-checkbox'),
		toDoCheck = checkList.children('input[type="checkbox"]');
	toDoCheck.each(function (index, element) {
		var $this = $(element),
			taskItem = $this.closest('.task-block');
		$this.on('click', function (e) {
			taskItem.toggleClass('task-checked');
		});
	});
})(jQuery);
// Tasks Important Active
$('.task-actions').on('click', '.important', function () {
	$(this).toggleClass('active');
});
// Tasks Important Active
$('.task-actions').on('click', '.star', function () {
	$(this).toggleClass('active');
});
// Countdown
$(document).ready(function () {
	countdown();
	setInterval(countdown, 1000);
	function countdown() {
		var now = moment(), // get the current moment
			// May 28, 2013 @ 12:00AM
			then = moment([2020, 10, 7]),
			// get the difference from now to then in ms
			ms = then.diff(now, 'milliseconds', true);
		// If you need years, uncomment this line and make sure you add it to the concatonated phrase
		/*
		years = Math.floor(moment.duration(ms).asYears());
		then = then.subtract('years', years);
		*/
		// update the duration in ms
		ms = then.diff(now, 'milliseconds', true);
		// get the duration as months and round down
		// months = Math.floor(moment.duration(ms).asMonths());

		// // subtract months from the original moment (not sure why I had to offset by 1 day)
		// then = then.subtract('months', months).subtract('days', 1);
		// update the duration in ms
		ms = then.diff(now, 'milliseconds', true);
		days = Math.floor(moment.duration(ms).asDays());

		then = then.subtract(days, 'days');
		// update the duration in ms
		ms = then.diff(now, 'milliseconds', true);
		hours = Math.floor(moment.duration(ms).asHours());

		then = then.subtract(hours, 'hours');
		// update the duration in ms
		ms = then.diff(now, 'milliseconds', true);
		minutes = Math.floor(moment.duration(ms).asMinutes());

		then = then.subtract(minutes, 'minutes');
		// update the duration in ms
		ms = then.diff(now, 'milliseconds', true);
		seconds = Math.floor(moment.duration(ms).asSeconds());

		// concatonate the variables
		diff = '<div class="num">' + days + ' <span class="text"> Days Left</span></div>';
		$('#daysLeft').html(diff);
	}
});
// Bootstrap JS ***********
// Tooltip
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
$(function () {
	$('[data-toggle="popover"]').popover()
})
// Custom Sidebar JS
jQuery(function ($) {
	// Dropdown menu
	$(".sidebar-dropdown > a").click(function () {
		$(".sidebar-submenu").slideUp(200);
		if ($(this).parent().hasClass("active")) {
			$(".sidebar-dropdown").removeClass("active");
			$(this).parent().removeClass("active");
		} else {
			$(".sidebar-dropdown").removeClass("active");
			$(this).next(".sidebar-submenu").slideDown(200);
			$(this).parent().addClass("active");
		}
	});
	//toggle sidebar
	$("#toggle-sidebar").click(function () {
		$(".page-wrapper").toggleClass("toggled");
	});
	// Pin sidebar on click
	$("#pin-sidebar").click(function () {
		if ($(".page-wrapper").hasClass("pinned")) {
			// unpin sidebar when hovered
			$(".page-wrapper").removeClass("pinned");
			$('.logo').css('visibility', 'visible');
			$("#sidebar").unbind("hover");

		} else {
			$(".page-wrapper").addClass("pinned");
			$('.logo').css('visibility', 'hidden');
			$("#sidebar").off('hover').hover(
				function () {
					// console.log("mouseenter");
					$(".page-wrapper").addClass("sidebar-hovered");
				},
				function () {
					// console.log("mouseout");
					$(".page-wrapper").removeClass("sidebar-hovered");
				}
			)
		}
	});
	// Pinned sidebar
	$(function () {
		$("#sidebar").hover(
			function () {
				// console.log("mouseenter");
				$(".page-wrapper").addClass("sidebar-hovered");
				$(".page-wrapper").hasClass("pinned") ? $('.logo').css('visibility', 'visible') : '';
			},
			function () {
				// console.log("mouseout");
				$(".page-wrapper").removeClass("sidebar-hovered");
				$(".page-wrapper").hasClass("pinned") ? $('.logo').css('visibility', 'hidden') : '';
			}
		)
	});
	// Toggle sidebar overlay
	$("#overlay").click(function () {
		$(".page-wrapper").toggleClass("toggled");
	});
	// Added by Srinu 
	$(function () {
		// When the window is resized, 
		$(window).resize(function () {
			// When the width and height meet your specific requirements or lower
			if ($(window).width() <= 768) {
				$(".page-wrapper").removeClass("pinned");
			}
		});
		// When the window is resized, 
		$(window).resize(function () {
			// When the width and height meet your specific requirements or lower
			if ($(window).width() >= 768) {
				$(".page-wrapper").removeClass("toggled");
			}
		});
	});
});
//this will set all alert messages with class 'alert' in slow fade out
setTimeout(function () {
	$('.alert').fadeOut('slow');
}, 2000);
//this will disable clicking outside of a modal in overall project
$('.modal').attr({
	'data-backdrop': "static",
	'data-keyboard': "false"
});
//this will disable auto complete in all input fields
$('input').attr('autocomplete', 'off');
// this will prevent normal window.alert messages to set it as swal
window.alert = function (message) {
	Swal.fire({
		text: message,
		target: 'body',
		toast: true,
		position: 'top-right',
		timer: 2000,
		showConfirmButton: true,
		confirmButtonColor: 'var(--primary-color)',
		timerProgressBar: true,
	})
	return false;
};
////////// Show Loader if ajax function is called inside anywhere in entire project  ////////
$(document).ajaxStart(function () {
	showOverlay();
});
$(document).ajaxStop(function () {
	hideOverlay();
});
showOverlay();
window.addEventListener('load', function () {
	hideOverlay();
});
// Function to add the overlay
function showOverlay() {
	var overlayDiv = document.createElement('div');
	overlayDiv.classList.add('overlay');
	document.body.appendChild(overlayDiv);

	var loaderDiv = document.createElement('div');
	loaderDiv.classList.add('loader');
	overlayDiv.appendChild(loaderDiv);

	var overlayText = document.createElement('span');
	overlayText.classList.add('overlay-text');
	overlayText.innerText = 'Please Wait';
	overlayDiv.appendChild(overlayText);
}
// Function to remove the overlay and clear the timer
function hideOverlay() {
	var overlayDiv = document.querySelector('.overlay');
	overlayDiv.remove();
}
//Initializing datatable by calling this function for every table.
function setdtable(table_id) {
	$(table_id).DataTable({
		'processing': true,
		'iDisplayLength': 10,
		"lengthMenu": [
			[10, 25, 50, -1],
			[10, 25, 50, "All"]
		],
		"createdRow": function (row, data, dataIndex) {
			$(row).find('td:first').html(dataIndex + 1);
		},
		"drawCallback": function (settings) {
			this.api().column(0).nodes().each(function (cell, i) {
				cell.innerHTML = i + 1;
			});
		},
		dom: 'lBfrtip',
		buttons: [{
			extend: 'excel',
		},
		{
			extend: 'colvis',
			collectionLayout: 'fixed four-column',
		}
		],
	}); // this will initialize all tables with dtable class in project as DataTable
};
///Append Row in Table TBody after getting response from ajax. ///common for all table//// Just send table id, response from ajax, Column in table. 
function appendDataToTable(tableSelector, response, columnMapping) {
	$(tableSelector).DataTable().destroy();
	let tableBody = $(tableSelector + ' tbody');
	tableBody.empty(); // Clear any existing rows in the table body

	// Iterate over the JSON data
	$.each(response, function (index, item) {
		// Create a new table row
		var row = $('<tr>');
		// Iterate over the column mapping
		$.each(columnMapping, function (key, value) {
			// Create a table cell with the data
			if (value === 'sno') {
				$('<td>').text(index + 1).appendTo(row); // Add serial number
			} else if (item.hasOwnProperty(value)) {
				if (value === 'action') {
					// If the key is 'action', insert the HTML content directly
					$('<td>').html(item[value]).appendTo(row);
				}else if (value === 'upload') {
					// If the key is 'action', insert the HTML content directly
					$('<td>').html(item[value]).appendTo(row);
				}else {
					// Otherwise, insert the text content
					$('<td>').text(item[value]).appendTo(row);
				}
			}
		});
		// Append the row to the table body
		tableBody.append(row);
	});
}
//Swal alert section *************************
function swalSuccess(title, text) {
	Swal.fire({
		icon: 'success',
		title: title,
		text: text,
		showConfirmButton: false,
		timerProgressBar: true,
		timer: 2000,
	})
}
function swalError(title, text) {
	Swal.fire({
		icon: 'error',
		title: title,
		text: text,
		confirmButtonColor: 'var(--primary-color)',
	})
}
function swalInfo(title, text) {
	Swal.fire({
		icon: 'info',
		title: title,
		text: text,
		confirmButtonColor: 'var(--primary-color)',
	})
}
function swalConfirm(title, text, functionname, idvalue) {
	Swal.fire({
		title: title,
		text: text,
		icon: 'question',
		showCancelButton: true,
		confirmButtonColor: '#009688',
		cancelButtonColor: '#d33',
		cancelButtonText: 'No',
		confirmButtonText: 'Yes'
	}).then((result) => {
		if (result.isConfirmed) {
			functionname(idvalue);
		}
	});
}
function checkMobileNo(mobileno, selector) {
	let mobileNoRegex = /[6-9]{1}[0-9]{9}$/;
	if (!(mobileNoRegex.test(mobileno))) {
		swalError('Warning', 'Enter valid Mobile Number.')
		$('#' + selector).val('')
	}
}
function checkLandlineFormat(number, selector) {
	let regex = /^\d{8,12}$/; // Landline number format (8 to 12 digits)
	if (!regex.test(number)) {
		swalError('Warning', 'Enter a valid landline number.');
		$('#' + selector).val('');
	}
}
function validateEmail(emailInput, selector) {
	// Regular expression for email validation
	let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	if (!emailPattern.test(emailInput)) {
		swalError('Warning', 'Kindly enter valid Email address');
		$('#' + selector).val('');
	}
}