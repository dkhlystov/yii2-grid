$(function() {

	$(document).on('click', '.array-input > button', itemAddClick);
	$(document).on('click', '.array-input .item-remove', itemRemoveClick);

	function itemAddClick(e)
	{
		itemAdd($(this).prev());
	}

	function itemRemoveClick(e)
	{
		e.preventDefault();
		$(this).closest('tr').remove();
	}

	function itemAdd($table)
	{
		var $row = $($table.parent().data('arrayInputTemplate'));

		//index
		var idx = -1;
		$table.find('input, textarea, select').each(function() {
			var m = this.name.match(/.+\[(\d+)\]\[[^\]]+\]/),
				i = m === null ? -1 : parseInt(m[1]);

			if (i > idx) idx = i;
		});
		idx++;

		//properties
		$row.removeClass('hidden');
		$row.find('input, textarea, select').prop('disabled', false).each(function() {
			this.name = this.name.replace(/\[0\]/, '[' + idx + ']');
		});

		$table.find('tbody').append($row);
	}

});
