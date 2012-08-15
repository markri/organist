/**
 * Netvlies OMS
 * Uses jQuery 1.7
 * @module OMS
 */
window.OMS = (function () {
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
        /**
         * Document to call for changes
         * @property ajaxDoc
         * @type String
         * @default ''
         */
        this.ajaxDoc = this._table.attr('data-ajax-doc') || '';
        /**
         * If the table should sort serverside (with querystring) or clientside (javascript)
         * @property _serverSort
         * @private
         * @type Boolean
         */
        this._serverSort = (this._table.attr('data-server-sort') != undefined);
        
        if (this.ajaxDoc.length) {
            this.addButtons();
        }
        this._headerEvents();
        if (!this._serverSort && this._table.find('[data-default-sort]').length) {
            this.sort(this._table.find('[data-default-sort]')[0], this._table.find('[data-default-sort]').attr('data-default-sort'));
        }
    };
    
    /**
     * Sorts the table with the given column and sort order
     * @method sort
     * @param {HTMLTableCellElement} columnHeader The column to sort by
     * @param {String} sortOrder The sort order ('asc' or 'desc')
     * @param {Function} callback Callback function which fires after sorting the table
     */
    OMS.SortableTable.prototype.sort = function (columnHeader, sortOrder, callback) {
        var sortQuery = window.location.search;
        
        function removeQueryStringPart(url, param) {
            var re = new RegExp("([?|&])" + param + "=.*?(&|$)","i");
            if (url.match(re)) {
                return url.replace(re, '$2');
            } else {
                return url;
            }
        }
        function replaceQueryStringPart(url, param, value) {
            var re = new RegExp("([?|&])" + param + "=.*?(&|$)","i");
            if (url.match(re)) {
                return url.replace(re,'$1' + param + "=" + value + '$2');
            } else {
                return url + '&' + param + "=" + value;
            }
        }
        
        if (typeof sortOrder === 'undefined' || !sortOrder.length) {
            sortOrder = 'asc';
        }
        if(this._serverSort) {
            if (!sortQuery.length) {
                sortQuery = '?sort-' + $(columnHeader).html() + '=' + sortOrder;
            } else {
                sortQuery = removeQueryStringPart(sortQuery, 'sort-' + $(columnHeader).html());
                if (!sortQuery.length) {
                    sortQuery = '?sort-' + $(columnHeader).html() + '=' + sortOrder;					
                } else {
                    sortQuery = '?sort-' + $(columnHeader).html() + '=' + sortOrder + '&' + sortQuery.substring(1);
                }
            }
            this._table.find('thead th[data-sort-order]').each(function () {
                if ($(columnHeader).html() != $(this).html()) {
                    sortQuery = replaceQueryStringPart(sortQuery, 'sort-' + $(columnHeader).html(), $(this).attr('data-sort-order'));
                }
            });
            window.location.search = sortQuery;
        } else {
            this._sortColumn(this._sortValues(columnHeader, sortOrder), callback);
        }
    };
        
    /**
     * Adds edit, save and remove buttons
     * @method addButtons
     */
    OMS.SortableTable.prototype.addButtons = function () {
        var self = this;
        this._table.find('.icons').remove(); // remove buttons if they already exists
        this._table.children('thead').children('tr').append('<th data-not-sortable class="icons"></th>'); //add a new th for the above button
        this._table.children('tbody').children('tr').append('<td class="icons"></td>'); // add a new cell to put the buttons in
        this._table.filter(':not([data-no-delete])').find('tr:not([data-no-delete]) td.icons').append('<a href="" class="icon ir delete"">del</a>'); // create a delete button
        this._table.filter(':not([data-no-edit])').find('tr:not([data-no-edit]) td.icons').append('<a href="" class="icon ir edit">edit</a>'); // create an edit button
        
        this._table.on('click', '.delete', function (e) { //delete event
            $.get(self.ajaxDoc, { 
                action: 'delete', 
                id: $(this).closest('tr').attr('id').substring(5) 
            });
            $(this).closest('tr').fadeOut(300, function() { $(this).remove(); }); // remove row from list
            e.preventDefault();
        });
        this._table.on('click', '.edit', function (e) {
            var headerCells = $(this).closest('table').children('thead').children().children();
            $(this).closest('tr').children('td:not(.icons)').each(function () {
                $(this).html('<input type="text" name="' + headerCells.eq($(this).index()).text() + '" value="' + $(this).html() + '" />');
            });
            $(this).removeClass('edit').addClass('save');
            e.preventDefault();
        });
        this._table.on('click', '.save', function (e) {
            var send = {
                action : 'delete',
                id : $(this).closest('tr').attr('id').substring(5)
            };
            $(this).closest('tr').find(':input').each(function () {
                send[$(this).attr('name')] = $(this).val();
                $(this).parent().html($(this).val());
            });
            $.get(self.ajaxDoc, send, function(data) {
                console.log(data);
            });
            $(this).removeClass('save').addClass('edit');
            e.preventDefault();
        });
    };
    /**
     * Adds click events to the headers
     * @method _headerEvents
     * @private
     */
    OMS.SortableTable.prototype._headerEvents = function () {
        var currentList = this;
        $(this._table).children('thead').find('th:not([data-not-sortable])').addClass('sortable').click(function () {
            var sortOrder = 'asc',
                columnHeader = this;
            if ($(columnHeader).attr('data-sort-order') === 'asc') { sortOrder = 'desc'; }
            currentList.sort(columnHeader, sortOrder);
        });
    };
    /**
     * Sort the values of the column
     * @method _sortValues
     * @private
     * @param {HTMLTableCellElement} columnHeader The column to sort by
     * @param {String} sortOrder The sort order ('asc' or 'desc')
     * @return {Object} values sorted with original index 
     */
    OMS.SortableTable.prototype._sortValues = function (columnHeader, sortOrder) {
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
    };
    /**
     * Sort the table using the returned values from _sortValues 
     * @method _sortColumn
     * @private
     * @param {Object} values values sorted with original index
     * @param {Function} callback Callback function which fires after sorting the table
     */
    OMS.SortableTable.prototype._sortColumn = function (values, callback) {
        var $newBody = $('<tbody />'),
            $oldBody = this._table.children('tbody'),
            i,
            l;
        for (i = 0, l = values.length; i < l; i++) {
            $newBody.append($oldBody.children(':nth-child(' + (values[i].index + 1) + ')').clone());
        }
        $oldBody.replaceWith($newBody);
        if (typeof callback === 'function') {
            callback();
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
        
        if (this.ajaxDoc.length && this._table.is(':visible')) {
            this.loadRows();
        }
    };
    /**
     * Loads the rows from the ajaxDoc and adds them to the tbody
     * @method loadRows
     * @param {Function} callback Callback function which fires after adding the results in the DOM
     */
    OMS.InfiniteScroll.prototype.loadRows = function (callback) {
        if (!this._loading) {
            var cells = '',
                self = this;
            this._loading = true;
            self._table.after('<span class="loader">loading...</span>');
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
                        self._table.parent().children('.loader').remove();
                        self._tableBody.append(
                            $('<tr />').attr('id', a).html(cells)
                        );
                        self._loading = false;
                    });
                    if (typeof callback === 'function') {
                        callback();
                    }
                    if (self._table.is(':visible') && ($(window).scrollTop() + $(window).height()) > (self._table.offset().top + self._table.height())) {
                        self.loadRows(callback);
                    }
                } else { //no more results
                    self.removeScrollEvents();
                    self._table.parent().children('.loader').remove();
                    self._table.after('<div class="no-more-results">Geen resultaten meer</div>');
                    if (typeof callback === 'function') {
                        callback();
                    }
                }
            });
        }
    };
    /**
     * Adds the scroll events to the window for loading new rows when the bottom is in view
     * @method addScrollEvents
     */
    OMS.InfiniteScroll.prototype.addScrollEvents = function () {
        var self = this,
            table = this._table;
        
        $(window).on('scroll', function (e) {
            if (($(this).scrollTop() + $(this).height()) > (table.offset().top + table.height())) {
                self.loadRows();
            }
        });
    };
    /**
     * Removes the scroll events from the window
     * @method removeScrollEvents
     */
    OMS.InfiniteScroll.prototype.removeScrollEvents = function () {
        $(window).off('scroll');
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
    /**
     * Adds the keyup and blur event to the input for loading and showing the results
     * @method addEvents
     */
    OMS.AutoComplete.prototype.addEvents = function () {
        var self = this,
            jqXHR,
                typWait;
        this._input.keyup(function () {
            clearTimeout(typWait);
            typWait = setTimeout(function () {
                if (jqXHR) { //Removes the current request.
                    jqXHR.abort();
                }
                jqXHR = $.getJSON(self.ajaxDoc, {
                    text: self._input.val()
                }, function (data) {
                    if(!$('.autocomplete-data').length) {
                        $('body').append($('<ul class="autocomplete-data"> /').css({
                            top: self._input.offset().top + self._input[0].offsetHeight,
                            left: self._input.offset().left
                        }));
                    }
                    $('.autocomplete-data').empty();
                    $.each(data, function (a, b) {
                        $('.autocomplete-data').append($('<li />').html(b));
                    });
                    $('.autocomplete-data li').click(function () {
                        self._input.val($(this).text());
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
        this._dragElement = undefined;
        /**
         * The element that is being hovered when dragging.
         * @property _targetElement
         * @private
         * @type jQueryObject
         */
        this._targetElement = undefined;		
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
        this._alertTimer = undefined;
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
        this.ajaxResultElement = undefined;
        
        if (this.ajaxDoc.length && this._tree.attr('data-ajax-results')) {
            this.ajaxResultElement = $('#'+this._tree.attr('data-ajax-results'));
        }
        
        this._addEvents();
    };
    OMS.Tree.prototype._addEvents = function () {
        var self = this;
        
        this._tree.on('mousedown', 'li', function (e) {
            if ($(this).attr('data-no-drag') !== undefined) { return false; }
            self._dragElement = $(this);
            self._startX = e.pageX;
            self._startY = e.pageY;
            self._elemX = self._dragElement[0].offsetLeft;
            self._elemY = self._dragElement[0].offsetTop;
            self._dragElement.before(self._dragElement.clone().addClass('clone'));
            self._listItems = self._tree.find('li');
            self._dragElement.addClass('dragging').css({
                    left: e.pageX - self._startX + self._elemX,
                    top: e.pageY - self._startY + self._elemY
            });
            
            //Removes default events to avoid text selection
            document.onselectstart = function() { return false; };
            document.ondragstart = function() { return false; };
            
            $(window).on('mousemove',function (e) { self._moveElement(e, self); });
            $(window).on('mouseup',function (e) { self._placeElement(e, self); });
            
            return false;
        });
        
        $(document).on('click', '.undo-save', function (e) {
            clearTimeout(self._alertTimer);
            $('.alert-bar').html('cancelled!').delay(500).animate({
                'height': 0,
                'line-height': 0
            }, function () {
                $(this).remove();
            });
            e.preventDefault();
        });
        this._tree.on('click', 'a', function(e) {
            e.preventDefault();
        });
        $('[data-undo-tree=' + this._tree.attr('id') + ']').click(function (e) {
            self.undo(e, self);
        });
        $('[data-save-tree=' + this._tree.attr('id') + ']').click(function (e) {
            self.save(e, self);
        });
    };
    /**
     * Move the current element and find the target element
     * @method _moveElement
     * @private
     * @param {Object} e The event
     * @param {Object} self The current tree object
     */
    OMS.Tree.prototype._moveElement = function (e, self) {
        var $item,
            itemTop,
            mouseY = e.pageY,
            position = '';
                
        //Find the target
        self._listItems.each(function () {
            $item = $(this);
            itemTop = $item.offset().top;
            if (mouseY > itemTop && mouseY < itemTop + self.itemHeight && $item[0] != self._dragElement[0]) {
                self._targetElement = $item;
                if (mouseY < itemTop + 8) {
                    position = 'top';
                } else if (mouseY > itemTop + self.itemHeight - 8) {
                    position = 'bottom';						
                } else if (mouseY > itemTop + 8 && mouseY < itemTop + self.itemHeight - 8) {
                    position = 'on';
                }
            }
        });
        //target must not be the clone
        this._listItems.removeClass('target top bottom on');
        if (this._targetElement && 
                this._targetElement.filter(':not(.clone)').length && 
                !this._targetElement.closest('.clone').length &&
                (position !== 'on' || this._targetElement.attr('data-no-children') === undefined) &&
                ((position !== 'top' && position !== 'bottom') || this._targetElement.attr('data-no-siblings') === undefined)) {
            this._targetElement.addClass('target').addClass(position);
        }
        this._dragElement.css( {
            left: e.pageX - self._startX + self._elemX,
            top: e.pageY - self._startY + self._elemY
        });
    };
    /**
     * Places the element on the position targeted
     * @method _placeElement
     * @private
     * @param {Object} e The event
     * @param {Object} self The current tree object
     */
    OMS.Tree.prototype._placeElement = function(e, self) {
        $('.clone').remove();
        self._listItems = self._tree.find('li');
        self._dragElement.removeClass('dragging');
        self._dragElement.css({
            left: 0,
            top: 0
        });
        
        if(self._listItems.filter('.top, .bottom, .on').length) { //change tree when new position
            
            //add to history
            self.history[self.history.length || 0] = {
                'node': self._dragElement.attr('id'),
                'parent': self._dragElement.closest('li').attr('id'),
                'prevSibling': self._dragElement.prev().attr('id')
            };
        
            var $clone = self._dragElement.clone().css({left:0,top:0});
            self._dragElement.remove();
            
            if (self._listItems.filter('.top').length) {
                self._targetElement.before($clone);
            } else if (self._listItems.filter('.bottom').length) {
                self._targetElement.after($clone);
            } else if (self._listItems.filter('.on').length) {
                if(!self._targetElement.has('ul').length) {
                    self._targetElement.append('<ul />');
                }
                self._targetElement.children('ul').append($clone);
            }
            self._tree.find('ul').filter(function (index) { 
                    return $(this).children().length < 1; 
            }).remove();
            

        }
        
        //remove classes and reset eventListeners
        self.listItems = self._tree.find('li');
        self.listItems.removeClass('target top bottom on');
        document.onselectstart = null;
        document.ondragstart = null;
        $(window).off('mousemove');
        $(window).off('mouseup');
        return false;
    };
    /**
     * Reverts the last change done in the tree and removes it from the history;
     * @method undo
     * @param {Object | Function} eCallback The event or a callback
     * @param {Object} self The current tree object
     */
    OMS.Tree.prototype.undo = function (eCallback, self) {
        var change = self.history.pop(),
            node = change && change.node ? $('#' + change.node) : undefined,
            parent = change && change.parent ? $('#' + change.parent) : undefined,
            sibling = change && change.prevSibling ? $('#' + change.prevSibling) : undefined;
        if (sibling) {
            sibling.after(node);
        } else if (parent) {
            parent.children('ul').prepend(node);
        } else {
            self._tree.prepend(node);
        }
        if (typeof eCallback === 'object') { eCallback.preventDefault(); }
        else if (typeof eCallback === 'function') { eCallback(); }
    };
    /**
     * Call the ajax-doc to save the tree
     * @method save
     * @param {Object | Function} eCallback The event or a callback
     * @param {Object} self The current tree object
     */
    OMS.Tree.prototype.save = function (eCallback, self) {
        if (self === undefined) { self = this; }
        if (!$('.alert-bar.tree-save').length) {
            $('.alert-bar').remove();
            $('body').append($('<div class="alert-bar tree-save" />')
                .html('Uw wijzigingen worden opgeslagen. <a href="#" class="undo-save">annuleren</a>')
                .animate({
                    'height': 40,
                    'line-height': '40px'
                }));
        }
        clearTimeout(self._alertTimer);
        self._alertTimer = setTimeout(function () {
            $.ajax({
                url: self.ajaxDoc,
                success: function (msg) {
                    self.history = [];
                    if (self.ajaxResultElement) {
                        self.ajaxResultElement.html(msg);
                    }
                }
            });
            $('.alert-bar').animate({
                'height': 0,
                'line-height': 0
            }, function () {
                $(this).remove();
            });
        }, self.alertTimeout);
        if (typeof eCallback === 'object') { eCallback.preventDefault(); }
        else if (typeof eCallback === 'function') { eCallback(); }
    };
    return OMS;
}());