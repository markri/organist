/**
 * Netvlies OMS
 * Uses jQuery 1.6.2
 * @module OMS
 */
window.OMS = function () {
	"use strict";	
	var OMS = {};
	
	/**
	 * Sortable table class that allows the user to order a table
	 * <p>Usage: <code>var sTable = new OMS.SortableTable($('table')[0]);</code></p>
	 * @class SortableTable
	 * @namespace OMS
	 * @constructor
	 * @param {HTMLTableElement} context <code>&lt;table&gt;</code> to use
	 */
	OMS.SortableTable = function (context) {		
		/**
		 * jQuery reference to the <code>&lt;table&gt;</code>
		 * @property _table
		 * @private
		 * @type jQueryObject
		 */
		this._table = $(context).addClass('sortable');

		this._headerEvents();
		if (this._table.find('[data-default-sort]').length) {
			this.sort(this._table.find('[data-default-sort]')[0], this._table.find('[data-default-sort]').attr('data-default-sort'));
		}
	};
	OMS.SortableTable.prototype = {
		/**
		 * Sorts the table with the given column and sort order
		 * @method sort
		 * @param {HTMLTableCellElement} columnHeader The column to sort by
		 * @param {String} sortOrder The sort order ('asc' or 'desc')
		 * @param {Function} callback Callback function which fires after sorting the table
		 */
		sort: function (columnHeader, sortOrder, callback) {
			if (typeof sortOrder === 'undefined' || !sortOrder.length) {
				sortOrder = 'asc';
			}
			this._sortColumn(this._sortValues(columnHeader, sortOrder), callback);
		},
		/**
		 * Adds click events to the headers
		 * @method _headerEvents
		 * @private
		 */
		_headerEvents: function () {
			var currentList = this;
			$(this._table).children('thead').find('th:not([data-not-sortable])').addClass('sortable').click(function () {
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
				index = $(columnHeader).index();
				
			$(columnHeader).attr('data-sort-order', sortOrder);
			//push all values in the column with their index in the array
			this._table.children('tbody').children('tr').children(':nth-child(' + (index + 1) + ')').each(function () {
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
		 * @param {Function} callback Callback function which fires after sorting the table
		 */
		_sortColumn: function (values, callback) {
			var $newBody = $('<tbody />'),
				$oldBody = this._table.children('tbody'),
				i = 0,
				l = values.length;
			for (; i < l; i++) {
				$newBody.append($oldBody.children(':nth-child(' + (values[i].index + 1) + ')').clone());
			}
			$oldBody.replaceWith($newBody);
			if (typeof callback === 'function') {
				callback();
			}
		}
	};

	/**
	 * A tabbed list with ajax calls.
	 * <p>Usage: <code>var tabs = new OMS.Tabs($('ul')[0]);</code></p>
	 * @class Tabs
	 * @constructor
	 * @param {HTMLUListElement} context <code>&lt;ul&gt;</code> to use
	 * @namespace OMS
	 */
	OMS.Tabs = function (context) {
		/**
		 * jQuery reference to the <code>&lt;ul&gt;</code>
		 * @property _tabs
		 * @private
		 * @type jQueryObject
		 */
		this._tabs = $(context).addClass('tabs');
		/**
		 * Element to put results in
		 * @property resultElem
		 * @type jQueryObject
		 * @default undefined
		 */
		this.resultElem = $('#' + this._tabs.attr('data-ajax-results'));
		if (!this.resultElem.length) {
			this.resultElem = undefined;
		}
		this._setClickEvents();
	};
	OMS.Tabs.prototype = {
		/**
		 * Sets the click events to the list items
		 * @method _setClickEvents
		 * @private
		 */
		_setClickEvents: function () {
			var thisObject = this,
			    $listItem,
			    jqXHR;
			this._tabs.find('li:not(.current)').live('click', function (e) {
				$listItem = $(this);
				
				//switches the current class
				$listItem.addClass('current').siblings().removeClass('current'); 
				//if the listitem has child listitems, but no child item with current class, make the first child the current
				if ($listItem.find('li').length && !$listItem.find('li.current').length) { 
					$listItem.find('li').first().addClass('current');
				}
				//if the listitem has a child listitem with the class current and a data-ajax-doc attribute, make it the current list item to get the data for
				if ($listItem.find('.current[data-ajax-doc]').length) {
					$listItem = $listItem.find('.current[data-ajax-doc]').last();
				}
				if ($listItem.attr('data-ajax-doc').length) {
					thisObject.resultElem.html('<span class="loader">loading...</span>');
					if (jqXHR) { //Removes the current request.
						jqXHR.abort();
					}
					jqXHR = $.ajax({
						type: 'GET',
						url: $listItem.attr('data-ajax-doc'),
						success: function (data) {
							thisObject.resultElem.html(data);
						},
						error: function (a, b, c) {
							thisObject.resultElem.html('<span class="error">ERROR: ' + c + '</span>');
						}
					});
				}
				e.preventDefault(); //to prevent links in list items to work
			});
		}
	};
	
	/** 
	 * A table that has an infinite scroll
	 * <p>Usage: <code>var infiniteScroll = new OMS.InfiniteScroll($('table')[0]);</code></p>
	 * @class InfiniteScroll
	 * @constructor
	 * @param {HTMLTableElement} context <code>&lt;table&gt;</code> to use
	 * @namespace OMS
	 */
	OMS.InfiniteScroll = function (context) {		
		/**
		 * Reference to the <code>&lt;table&gt;</code>
		 * @property _table
		 * @private
		 * @type HTMLTableElement
		 */
		this._table = $(context).addClass('infinite-scroll');
		/**
		 * The <code>&lt;tbody&gt;</code> of the table
		 * @property _tableBody
		 * @private
		 * @type HTMLTableRowElement
		 */
		this._tableBody = this._table.children('tbody');
		/**
		 * Document to get the new rows
		 * @property ajaxDoc
		 * @type String
		 * @default ''
		 */
		this.ajaxDoc = this._table.attr('data-ajax-doc') || '';
		/**
		 * Maximum number of rows to get
		 * @property maxRows
		 * @type Number
		 * @default 10
		 */
		this.maxRows = parseInt((this._table.attr('data-number-of-rows') || 10), 10);
		/**
		 * Text to show when there are no more results
		 * @property noMoreResults
		 * @type String
		 * @default 'Geen resultaten meer'
		 */
		this.noMoreResults = this._table.attr('data-no-more-results') || 'Geen resultaten meer';
		/**
		 * If new rows are loading
		 * @property _loading
		 * @private
		 * @type Boolean
		 * @default false
		 */		 
		this._loading = false;	
		
		this.addScrollEvents();
		
		if (this.ajaxDoc.length && this._table.filter('[data-init-load]').length) {
			this.loadRows();
		}
	};
	OMS.InfiniteScroll.prototype = {
		/**
		 * Loads the rows from the ajaxDoc and adds them to the tbody
		 * @method loadRows
		 * @param {Function} callback Callback function which fires after adding the results in the DOM
		 */
		loadRows: function (callback) {
			if (!this._loading) {
				var cells = '',
				    thisObject = this;
				this._loading = true;
				this._tableBody.append('<span class="loader">loading...</span>');
				$.getJSON(this.ajaxDoc, {
					rows: this.maxRows,
					loadedRows: this._tableBody.children('tr').length,
					lastId: this._tableBody.children('tr').last().attr('id') || ''
				}, function (data) {
					if (data) {
						$.each(data, function (a, b) { //loop through rows
							cells = '';
							$.each(b, function (c, d) { //loop through cells
								cells += '<td>' + d + '</td>';
							});
							thisObject._tableBody.children('.loader').remove();
							thisObject._tableBody.append(
								$('<tr />').attr('id', a).html(cells)
							);
							thisObject._loading = false;
						});
					} else { //no more results
						thisObject.removeScrollEvents();
						thisObject._tableBody.children('.loader').remove();
						thisObject._table.after('<div class="no-more-results">Geen resultaten meer</div>');
					}
					if (typeof callback === 'function') {
						callback();
					}
				});
			}
		},
		/**
		 * Adds the scroll events to the window for loading new rows when the bottom is in view
		 * @method addScrollEvents
		 */
		addScrollEvents: function () {
			var thisObject = this,
			    table = this._table;
			
			$(window).bind('scroll', function (e) {
				if (($(this).scrollTop() + $(this).height()) > (table.offset().top + table.height())) {
					thisObject.loadRows();
				}
			});
		},
		/**
		 * Removes the scroll events from the window
		 * @method removeScrollEvents
		 */
		removeScrollEvents: function () {
			$(window).unbind('scroll');
		}
	};
	
	/** 
	 * An input field that can complete text while typing
	 * <p>Usage: <code>var autoCompleteField = new OMS.InfiniteScroll($('input')[0]);</code></p>
	 * @class AutoComplete
	 * @constructor
	 * @param {HTMLInputElement} context <code>&lt;input&gt;</code> to use
	 * @namespace OMS
	 */
	OMS.AutoComplete = function (context) {
		/**
		 * jQuery Reference to the <code>&lt;input&gt;</code>
		 * @property _input
		 * @private
		 * @type jQueryObject
		 */
		this._input = $(context).addClass('autocomplete');
		/**
		 * Document to call for the results
		 * @property ajaxDoc
		 * @type String
		 * @default ''
		 */
		this.ajaxDoc = this._input.attr('data-ajax-doc') || '';
		
		if(this.ajaxDoc) {
			this.addEvents();
		}
	};
	OMS.AutoComplete.prototype = {
		/**
		 * Adds the keyup and blur event to the input for loading and showing the results
		 * @method addEvents
		 */
		addEvents: function () {
			var thisObject = this,
			    jqXHR,
					typWait;
			this._input.keyup(function () {
				clearTimeout(typWait);
				typWait = setTimeout(function () {
					if (jqXHR) { //Removes the current request.
						jqXHR.abort();
					}
					jqXHR = $.getJSON(thisObject.ajaxDoc, {
						text: thisObject._input.val()
					}, function (data) {
						if(!$('.autocomplete-data').length) {
							$('body').append($('<ul class="autocomplete-data"> /').css({
								top: thisObject._input.offset().top + thisObject._input[0].offsetHeight,
								left: thisObject._input.offset().left
							}));
						}
						$('.autocomplete-data').empty();
						$.each(data, function (a, b) {
							$('.autocomplete-data').append($('<li />').html(b));
						});
						$('.autocomplete-data li').click(function () {
							thisObject._input.val($(this).text());
						});
					});
				}, 300);
			});
			this._input.blur(function () {
				clearTimeout(typWait);
				if (jqXHR) {
					jqXHR.abort();
				}
				setTimeout(function () {
					$('.autocomplete-data').fadeOut(function () {
						$(this).remove();
					});
				}, 200);
			});
		}
	};
	
	/**
	 * A list with drag and drop functionalities
	 * <p>Usage: <code>var dndTree = new OMS.Tree($('ul')[0]);</code></p>
	 * @class Tree
	 * @constructor
	 * @param {HTMLUListElement} context <code>&lt;ul&gt;</code> to use
	 * @namespace OMS
	 */
	OMS.Tree = function (context) {
		/**
		 * jQuery reference to the <code>&lt;ul&gt;</code>
		 * @property _tree
		 * @private
		 * @type jQueryObject
		 */
		this._tree = $(context).addClass('dnd-tree');
		/**
		 * jQuery reference to the list items in the _tree
		 * @property _listItems
		 * @private
		 * @type jQueryArray
		 */
		this._listItems = this._tree.find('li');
		/**
		 * The horizontal coordinate of the mouse relative to whole document on initialisation of the drag.
		 * @property _startX
		 * @private
		 * @type Number
		 */
		this._startX = 0;
		/**
		 * The vertical coordinate of the mouse relative to whole document on initialisation of the drag.
		 * @property _startY
		 * @private
		 * @type Number
		 */
		this._startY = 0;
		/**
		 * The horizontal coordinate of the dragging to _tree on initialisation of the drag.
		 * @property _elemX
		 * @private
		 * @type Number
		 */
		this._elemX = 0;
		/**
		 * The vertical coordinate of the dragging to _tree on initialisation of the drag.
		 * @property _elemY
		 * @private
		 * @type Number
		 */
		this._elemY = 0;
		/**
		 * The element that is being dragged.
		 * @property _dragElement
		 * @private
		 * @type jQueryObject
		 */
		this._dragElement;
		/**
		 * The element that is being hovered when dragging.
		 * @property _targetElement
		 * @private
		 * @type jQueryObject
		 */
		this._targetElement;		
		/**
		 * Array which holds the tree's edit hisory
		 * @property history
		 * @type Object
		 */
		this.history = [];
		/**
		 * The (line-)height of a list item without children lists.
		 * @property itemHeight
		 * @type Number
		 * @default 32
		 */
		this.itemHeight = 32;
		/**
		 * The timeout for the alert-bar
		 * @property _alertTimer
		 * @private
		 * @type Number
		 */
		this._alertTimer;
		/**
		 * The time to wait until ajaxDoc is called.
		 * @property alertTimeout
		 * @type Number
		 * @default 6000
		 */
		this.alertTimeout = parseInt((this._tree.attr('data-save-wait') || 6000), 10);
		/**
		 * Document to call to save
		 * @property ajaxDoc
		 * @type String
		 * @default ''
		 */
		this.ajaxDoc = this._tree.attr('data-ajax-doc') || '';
		/**
		 * The element where the ajax resultt is placed in.
		 * @property ajaxResultElement
		 * @type jQueryElement
		 * @default undefined
		 */
		this.ajaxResultElement;
		if (this.ajaxDoc.length && this._tree.attr('data-ajax-results')) {
			this.ajaxResultElement = $('#'+this._tree.attr('data-ajax-results'));
		}
		
		this._addEvents();
	};
	OMS.Tree.prototype = {
		/**
		 * Adds the events to the elements
		 * @method _addEvents
		 * @private
		 */
		_addEvents: function () {
			var thisObject = this;
			
			this._listItems.live('mousedown', function (e) {
				if ($(this).attr('data-no-drag') !== undefined) { return false; }
				thisObject._dragElement = $(this);
				thisObject._startX = e.pageX;
				thisObject._startY = e.pageY;
				thisObject._elemX = thisObject._dragElement[0].offsetLeft;
				thisObject._elemY = thisObject._dragElement[0].offsetTop;
				thisObject._dragElement.before(thisObject._dragElement.clone().addClass('clone'))
				thisObject._listItems = thisObject._tree.find('li');
				thisObject._dragElement.addClass('dragging').css({
						left: e.pageX - thisObject._startX + thisObject._elemX,
						top: e.pageY - thisObject._startY + thisObject._elemY
				});
				
				//Removes default events to avoid text selection
				document.onselectstart = function() { return false; };
				document.ondragstart = function() { return false; };
				
				$(window).bind('mousemove',function (e) { thisObject._moveElement(e, thisObject) });
				$(window).bind('mouseup',function (e) { thisObject._placeElement(e, thisObject) });
				
				return false;
			});
			
			$('.undo-save').live('click', function (e) {
				clearTimeout(thisObject._alertTimer);
				$('.alert-bar').html('cancelled!').delay(500).animate({
					'height': 0,
					'line-height': 0
				}, function () {
					$(this).remove();
				});
				e.preventDefault();
			});
			this._tree.find('a').live('click', function(e) {
				e.preventDefault();
			});
			$('[data-undo-tree='+this._tree.attr('id')+']').click(function (e) {
				thisObject.undo(e, thisObject);
			});
			$('[data-save-tree='+this._tree.attr('id')+']').click(function (e) {
				thisObject.save(e, thisObject);
			});
		},
		/**
		 * Move the current element and find the target element
		 * @method _moveElement
		 * @private
		 * @param {Object} e The event
		 * @param {Object} thisObject The current tree object
		 */
		_moveElement : function (e, thisObject) {
			var $item,
			    itemTop,
			    mouseY = e.pageY,
			    position = '';
					
			//Find the target
			thisObject._listItems.each(function () {
				$item = $(this);
				itemTop = $item.offset().top;
				if (mouseY > itemTop && mouseY < itemTop + thisObject.itemHeight && $item[0] != thisObject._dragElement[0]) {
					thisObject._targetElement = $item;
					if (mouseY < itemTop + 8) {
						position = 'top';
					} else if (mouseY > itemTop + thisObject.itemHeight - 8) {
						position = 'bottom';						
					} else if (mouseY > itemTop + 8 && mouseY < itemTop + thisObject.itemHeight - 8) {
						position = 'on';
					}
				}
			});
			//target must not be the clone
			this._listItems.removeClass('target top bottom on');
			if (this._targetElement && 
					this._targetElement.filter(':not(.clone)').length && 
					!this._targetElement.parents('.clone').length &&
					(position !== 'on' || this._targetElement.attr('data-no-children') === undefined) &&
					((position !== 'top' && position !== 'bottom') || this._targetElement.attr('data-no-siblings') === undefined)) {
				this._targetElement.addClass('target').addClass(position);
			}
			this._dragElement.css( {
				left: e.pageX - thisObject._startX + thisObject._elemX,
				top: e.pageY - thisObject._startY + thisObject._elemY
			});
		},
		/**
		 * Places the element on the position targeted
		 * @method _placeElement
		 * @private
		 * @param {Object} e The event
		 * @param {Object} thisObject The current tree object
		 */
		_placeElement : function(e, thisObject) {
			$('.clone').remove();
			thisObject._listItems = thisObject._tree.find('li');
			thisObject._dragElement.removeClass('dragging');
			thisObject._dragElement.css({
				left: 0,
				top: 0
			});
			
			if(thisObject._listItems.filter('.top, .bottom, .on').length) { //change tree when new position
				
				//add to history
				thisObject.history[thisObject.history.length || 0] = {
					'node': thisObject._dragElement.attr('id'),
					'parent': thisObject._dragElement.parents('li').first().attr('id'),
					'prevSibling': thisObject._dragElement.prev().attr('id')
				};
			
				var $clone = thisObject._dragElement.clone().css({left:0,top:0});
				thisObject._dragElement.remove();
				
				if (thisObject._listItems.filter('.top').length) {
					thisObject._targetElement.before($clone);
				} else if (thisObject._listItems.filter('.bottom').length) {
					thisObject._targetElement.after($clone);
				} else if (thisObject._listItems.filter('.on').length) {
					if(!thisObject._targetElement.has('ul').length) {
						thisObject._targetElement.append('<ul />');
					}
					thisObject._targetElement.children('ul').append($clone);
				}
				thisObject._tree.find('ul').filter(function (index) { 
						return $(this).children().length < 1; 
				}).remove();
				

			}
			
			//remove classes and reset eventListeners
			thisObject.listItems = thisObject._tree.find('li');
			thisObject.listItems.removeClass('target top bottom on');
			document.onselectstart = null;
			document.ondragstart = null;
			$(window).unbind('mousemove');
			$(window).unbind('mouseup');
			return false;
		},
		/**
		 * Reverts the last change done in the tree and removes it from the history;
		 * @method undo
		 * @param {Object | Function} eCallback The event or a callback
		 * @param {Object} thisObject The current tree object
		 */
		undo: function (eCallback, thisObject) {
			var change = thisObject.history.pop(),
				node = change && change.node ? $('#' + change.node) : undefined,
				parent = change && change.parent ? $('#' + change.parent) : undefined,
				sibling = change && change.prevSibling ? $('#' + change.prevSibling) : undefined;
			if (sibling) {
				sibling.after(node);
			} else if (parent) {
				parent.children('ul').prepend(node);
			} else {
				thisObject._tree.prepend(node);
			}
			if (typeof eCallback === 'object') { eCallback.preventDefault(); }
			else if (typeof eCallback === 'function') { eCallback(); }
		},
		/**
		 * Call the ajax-doc to save the tree
		 * @method save
		 * @param {Object | Function} eCallback The event or a callback
		 * @param {Object} thisObject The current tree object
		 */
		save: function (eCallback, thisObject) {
			if (thisObject === undefined) { thisObject = this; }
			if (!$('.alert-bar.tree-save').length) {
				$('.alert-bar').remove();
				$('body').append($('<div class="alert-bar tree-save" />')
					.html('Uw wijzigingen worden opgeslagen. <a href="#" class="undo-save">annuleren</a>')
					.animate({
						'height': 40,
						'line-height': '40px'
					}));
			}
			clearTimeout(thisObject._alertTimer);
			thisObject._alertTimer = setTimeout(function () {
				$.ajax({
					url: thisObject.ajaxDoc,
					success: function (msg) {
						thisObject.history = [];
						if (thisObject.ajaxResultElement) {
							thisObject.ajaxResultElement.html(msg);
						}
					}
				});
				$('.alert-bar').animate({
					'height': 0,
					'line-height': 0
				}, function () {
					$(this).remove();
				});
			}, thisObject.alertTimeout);
			if (typeof eCallback === 'object') { eCallback.preventDefault(); }
			else if (typeof eCallback === 'function') { eCallback(); }
		}
	};	
	return OMS;
}();