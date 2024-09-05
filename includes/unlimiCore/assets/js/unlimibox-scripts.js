var $ = jQuery;

class UnlimithmBox {
    prefix = '__unlimithm__';
    posX = 0;
    posY = 0;
    appendTo = 'body';
    wrapper = '#' + this.prefix + 'stylebox';

    handleEl = this.prefix + 'movebox';
    closeEl = this.prefix + 'closebox';
    saveEl = this.prefix + 'save';
    resetEl = this.prefix + 'reset';
    resetAllEl = this.prefix + 'resetall';
    cencelEl = this.prefix + 'cancel';
    actionEl = this.prefix + 'action';
    panelEl = this.prefix + 'pannel';
    highlightEl = this.prefix + 'highlight_element';
    handleElement = '.' + this.handleEl;
    closeElement = '.' + this.closeEl;
    saveElement = '.' + this.saveEl;
    resetElement = '.' + this.resetEl;
    resetAllElement = '.' + this.resetAllEl;
    cancelElement = '.' + this.cencelEl;
    actionElement = '.' + this.actionEl;
    panelElement = '.' + this.panelEl;
    highlightElement = '.' + this.highlightEl;

    dataType = 'type';
    dataInitValue = 'initval';

    constructor(options) {
        console.log('UnlimithmBox is ready!');

        this.options = options;

        this.start();
    }

    start() {
        let self = this;

        // use right click
        $(window).on('contextmenu', function(event) {

            // event.preventDefault();

            self.element = event.target;

            let pos = self._getMousePosition(event);
            self.posX = pos.x;
            self.posY = pos.y;

            if (!self.checkInit() && !self.checkOpenBox() && !self.checkCloseBox()) {
                self.init();
            }

            if (!self.isBox(event.target)) {
                event.preventDefault();
            }
        });
    }

    checkCloseBox() {
        return $(this.element).hasClass(this.closeEl);
    }

    async init() {
        let self = this;
        this.closeAll();

        let data = {};
        data.path = this._getAllParents();
        let response = await self._sendRequest('getBox', data);
        self.setBox(response.html);
        self.initDraggable();
        self.initClose();
        self.initSaveButton();
        self.initCancelButton();
        self.initResetButton();
        self.initResetAllButton();
        self.initActionFields();
    }

    checkInit() {
        return this.isBox(this.element) || $(this.element).attr('class') == this.closeEl;
    }

    checkOpenBox() {
        return $(this.wrapper).length > 0;
    }

    setBox(html) {

        $(this.appendTo).append(html);

        $(this.wrapper).css({ 'left': this.posX, 'top': this.posY });

        // this.setDefaultFieldsValues();
        this.highlight(false);
    }

    highlight(highlight = true) {
        if (highlight) {
            $(this.element).addClass(this.highlightEl);
        } else {
            $(this.highlightElement).removeClass(this.highlightEl);
        }
    }

    isBox(element) {
        return $(element).closest(this.wrapper).length > 0;
    }

    initClose() {
        let self = this;
        $(this.closeElement).click(function() {
            self.close(self.wrapper);
            self.highlight(false);
            self.saveAction(self.element);
        });
    }

    close(el) {
        $(el).remove();
    }

    closeAll() {
        this.close(this.wrapper);
    }

    initSaveButton() {
        let self = this;
        let changedKeyVersion = false;

        $(this.wrapper).find('[name=keytypes]').change(function() {
            changedKeyVersion = true;
        });

        $(this.saveElement).click(function() {
            // self.saveAction(changedKeyVersion);
            self.saveAction(true);
        });
    }

    initResetButton() {
        let self = this;

        $(this.resetElement).click(function() {
            self.confirmButton(this, self.resetAction(this));
        });
    }

    initResetAllButton() {
        let self = this;

        $(this.resetAllElement).click(function() {
            self.confirmButton(this, self.resetAllAction(this));
        });
    }

    confirmButton(element, callback) {
        let text = $(element).attr('data-confirm');
        if (confirm(text) == true && typeof callback == 'function') {
            callback();
        }
    }

    initCancelButton() {
        let self = this;
        $(this.cancelElement).click(function() {
            self.cancelAction(this);
        });
    }

    async saveAction(sendPath = false) {

        let self = this;
        let data = {};

        data.key = self.getKey();
        data.key_version = self.getKeyVersion();

        if (sendPath) {
            data.path = this._getAllParents();
        }
        
        $(`${this.actionElement}.changed`).each((index, el) => {
            let type = self.getDataValueType(el);
            data[type] = $(el).val();
        });

        await this._sendRequest('save', data);
    }

    async cancelAction() {
        await this._sendRequest('cancel');
    }

    async resetAction() {
        let self = this;
        let data = {};

        data.key = self.getKey();
        data.key_version = self.getKeyVersion();
        
        await this._sendRequest('reset', data);
    }

    async resetAllAction() {
        await this._sendRequest('resetall');
    }

    setStyleToElement(style, value) {
        let element = this.getCss();
        this._setCssStyle(value, style, element);
    }

    _setCssStyle(value, action, element) {
        let css = {};
        css[action] = value;
        $(element).css(css);
    }

    getCss() {
        return $(this.wrapper).find('[name=css]').val();
    }

    getKey() {
        return $(this.wrapper).find('[name=key]').val();
    }

    getKeyVersion() {
        return $(this.wrapper).find('[name=keytypes]:checked').val();
        // return $(this.wrapper).find('[name=key_version]').val();
    }

    initDraggable() {
        $(this.wrapper).draggable({
            handle: this.handleElement
        });
    }

    initActionFields() {
        let self = this;
        $(this.actionElement).on('change', function() {
            let value = $(this).val();
            let style = self.getDataValueType(this);
            self.setStyleToElement(style, value);
            // self._setDataActionType(this, 'change');
            self.markAsChanged(this);
        });
    }

    getActionFields(panel = null) {
        if (panel) {
            return $(this.wrapper).find(panel).find(this.actionElement);
        }
        return $(this.wrapper).find(this.actionElement);
    }

    // setDefaultFieldsValues() {
    //     let self = this;

    //     $(this.wrapper).find(this.actionElement).each((index, el) => {
    //         let v = self.getDataValueInit(el);
    //         if (v.length == 0) {
    //             let style = self.getDataValueType(el);
    //             let styleValue = $(self.element).css(style);
    //             v = self.convertRgbToHex(styleValue);
    //         }

    //         // TODO: check this part, it seems something is not correct 
    //         $(el).val(v);
    //     });
    // }

    convertRgbToHex(v) {
        if (typeof v == 'undefined' || v.indexOf('rgba') < 0 || v.indexOf('rgb') < 0) {
            return '';
        }

        let d = v.indexOf('rgba') < 0 ? 'rgb' : 'rgba';
        let rgb = v.split(d)[1].replace('(', '').replace(')', '').split(', ');

        let r = parseInt(rgb[0].trim());
        let g = parseInt(rgb[1].trim());
        let b = parseInt(rgb[2].trim())

        return this.rgbToHex(r, g, b);
    }

    componentToHex(c) {
        let hex = c.toString(16);
        return hex.length == 1 ? "0" + hex : hex;
    }

    rgbToHex(r, g, b) {
        return "#" + this.componentToHex(r) + this.componentToHex(g) + this.componentToHex(b);
    }

    async _sendRequest(action = '', data = {}, callback = null) {

        let response;
        switch (action) {
            case 'getBox':
                response = await this._request('getBox', data);
                break;
            case 'save':
                response = await this._request('save', data);
                this._saveControl(response);
                break;
            case 'cancel':
                location.reload();
                break;
            case 'reset':
                response = await this._request('remove', data);
                this._saveControl(response);
                break;
            case 'resetall':
                response = await this._request('removeall', data);
                console.log('RESET all');
                this._saveControl(response);
                break;
        }

        if (typeof callback == 'function') {
            callback(response);
        }

        return response;
    }

    _getAllParents(element = null) {
        let a = $(element || this.element);
        let elems = [];

        while (a.length > 0) {

            let eli = {};
            let elTagName = $(a).prop('tagName');

            if (elTagName == 'HTML') {
                break;
            }

            if (typeof elTagName == 'string') {

                eli.tagname = elTagName.toLowerCase();

                let elId = $(a).attr('id');
                if (typeof elId == 'string' && elId.length > 0) {
                    eli.id = elId;
                }

                let elClass = $(a).attr('class');
                if (typeof elClass == 'string' && elClass.length > 0) {
                    eli.class = elClass.split(' ');
                }

                elems.unshift(eli);
            }

            a = $(a).parent();
        }

        return elems;
    }

    _replaceAll(str, from, to) {
        if (typeof str != 'string') {
            return false;
        }
        return str.split(from).join(to);
    }

    markAsChanged(el) {
        $(el).addClass('changed');
    }

    getDataType() {
        return `data-${this.dataType}`;
    }

    getDataSelectorType() {
        return `[${this.getDataType()}]`;
    }

    getDataValueType(el) {
        return $(el).attr(this.getDataType());
    }

    getDataInit() {
        return `data-${this.dataInitValue}`;
    }

    getDataSelectorInit() {
        return `[${this.getDataInit()}]`;
    }

    getDataValueInit(el) {
        return $(el).attr(this.getDataInit()) || '';
    }

    setDataValueInit(el, value) {
        return $(el).attr(this.getDataInit(), value);
    }

    async _request(action, data, callback = null) {

        const res = await new Promise(function(resolve, reject) {

            $.ajax({
                type: 'POST',
                url: customizerAction.ajax_url,
                data: {
                    action: action,
                    nonce: customizerAction.nonce,
                    data: JSON.stringify(data),
                },
                dataType: 'json',
                cache: false,
                success: function(response) {
                    if (response.status == 'success') {
                        resolve(response.data);
                    } else {
                        reject(response.data);
                    }
                },
                error: function(errorThrown) {
                    reject(errorThrown);
                }
            });
        });

        // if (typeof callback == 'function') {
        //     callback(res);
        // }

        return res;
    }

    _saveControl(data) {
        this.options.customizer.preview.send('unlimiCustomizer', data);
    }

    ___displayFormData(formData) {
        // Display the key/value pairs
        for (var pair of formData.entries()) {
            console.log(pair[0] + ', ' + pair[1]);
        }
    }

    _getMousePosition(e) {

        let cursorX, cursorY;

        // check to see if you're using IE
        const IE = document.all ? true : false;

        if (IE) {
            //do if internet explorer
            cursorX = event.clientX + document.body.scrollLeft;
            cursorY = event.clientY + document.body.scrollTop;
        } else {
            //do for all other browsers
            cursorX = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
            cursorY = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
        }

        return { 'x': cursorX, 'y': cursorY };
    }
}