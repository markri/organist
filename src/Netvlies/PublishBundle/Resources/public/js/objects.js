/**
 * Netvlies OMS
 * Uses jQuery 1.6.2
 * @module OMS
 */
window.OMS = function ($) {
	"use strict";	
	var OMS = {};
	
	/**
	 * Sortable table class that allows the user to order a table
	 * <p>Usage: <code>new OMS.SortableTable($(this)[0]);<code></p>
	 * @class SortableTable
	 * @namespace OMS
	 * @constructor
	 * @param {HTMLTableElement} context <code>&lt;table&gt;</code> to use
	 */
	OMS.SortableTable = function(context) {		
		/**
		 * Reference to the <code>&lt;table&gt;</code>
		 * @property table
		 * @type HTMLTableElement
		 */
		this.table = $(context).addClass('sortable');

		this._headerEvents();
		if (this.table.find('[data-default-sort]').length) {
			this.sort(this.table.find('[data-default-sort]')[0], this.table.find('[data-default-sort]').attr('data-default-sort'));
		}
	}	
	OMS.SortableTable.prototype = {
		/**
		 * Sorts the table with the given column and sort order
		 * @method sort
		 * @param {HTMLTableCellElement} columnHeader The column to sort by
		 * @param {String} sortOrder The sort order ('asc' or 'desc')
		 */
		sort: function (columnHeader, sortOrder) {
			if (typeof sortOrder == 'undefined' || !sortOrder.length) {
				sortOrder = 'asc';
			}
			this._sortColumn(this._sortValues($(columnHeader), sortOrder));
		},
		
		/**
		 * Adds click events to the headers
		 * @method _headerEvents
		 * @private
		 */
		_headerEvents: function() {
			var currentList = this;
			$(this.table).children('thead').find('th:not([data-not-sortable])').addClass('sortable').click(function () {
				var sortOrder = 'asc',
					columnHeader = this;
				if ($(columnHeader).attr('data-sort-order') === 'asc') { sortOrder = 'desc'; }
				currentList.sort(columnHeader, sortOrder);
			});
		},
		/**
		 * Sort the values of the column
		 * @method _sortValues
		 * @private
		 * @param {HTMLTableCellElement} columnHeader The column to sort by
		 * @param {String} sortOrder The sort order ('asc' or 'desc')
		 * @return {Object} values sorted with original index 
		 */
		_sortValues: function (columnHeader, sortOrder) {
			var values = [],
				numbers = true,
				index = columnHeader.index();
				
			columnHeader.attr('data-sort-order', sortOrder);
			//push all values in the column with their index in the array
			this.table.children('tbody').children('tr').children(':nth-child(' + (index + 1) + ')').each(function () {
				values.push({name: $(this).text(), index: $(this).parent().index() });
				if (numbers) { numbers = !isNaN($(this).text()); }
			});
		
			//sort the values
			values.sort(function (a, b) {
				if (!numbers) { //string sort
					var nameA = a.name.toLowerCase(), 
						nameB = b.name.toLowerCase();
					if (sortOrder === 'asc') {
						if (nameA < nameB) { return -1; }
						if (nameA > nameB) { return 1; }
					} else {
						if (nameA < nameB) { return 1; }
						if (nameA > nameB) { return -1; }
					}
					return 0;
				} else { //numbers sort					
					if (sortOrder === 'asc') {
						return a.name - b.name;
					} else {
						return b.name - a.name;					
					}
				}
			});
			return values;
		},
		/**
		 * Sort the table using the returned values from _sortValues 
		 * @method _sortColumn
		 * @private
		 * @param {Object} values values sorted with original index
		 */
		_sortColumn: function (values) {
			var $newBody = $('<tbody />'),
				$oldBody = this.table.children('tbody'),
				i = 0,
				l = values.length;
			for (; i < l; i++) {
				$newBody.append($oldBody.children(':nth-child(' + (values[i].index + 1) + ')').clone());
			}
			$oldBody.replaceWith($newBody);
		}
	};

	/** 
	 * Long list (table) that has an infinite scroll
	 * <p>Usage: <code>new OMS.LongList($(this)[0]);</code></p>
	 * @class LongList
	 * @contructor
	 * @param {HTMLTableElement} context <code>&lt;table&gt;</code> to use
	 * @namespace OMS
	 */
	OMS.LongList = function(context) {		
		/**
		 * Reference to the <code>&lt;table&gt;</code>
		 * @property table
		 * @type HTMLTableElement
		 */
		this.table = $(context).addClass('longlist');
		/**
		 * The <code>&lt;tbody&gt;</code> of the table
		 * @property table
		 * @type HTMLTableRowElement
		 */
		this.tableBody = this.table.children('tbody');
		/**
		 * Document to get the new rows
		 * @property ajaxDoc
		 * @type String
		 * @default ''
		 */
		this.ajaxDoc = this.table.attr('data-ajax-doc') || '';
		/**
		 * Maximum number of rows to get
		 * @property maxRows
		 * @type Int
		 * @default 10
		 */
		this.maxRows = parseInt(this.table.attr('data-number-of-rows') || 10);
		
		if(this.ajaxDoc.length) {
			this.loadRows();
		}
	}	
	
	OMS.LongList.prototype = {
		loadRows: function () {
			$.getJSON(this.ajaxDoc, {
					rows: this.maxRows,
					loadedRows: this.tableBody.children('tr').length,
					lastId: this.tableBody.children('tr').last().attr('id') || ''
				},
				function (data) {
					$.each(data, function(a,b) {
						cells = '';
						$.each(b, function (c,d) {
							cells += '<td>' + d + '</td>';
						});
						$body.append(
							$('<tr />').attr('id', a).html(cells)
						);
					});
				}
			);
		}
	}
	return OMS;
}(jQuery);