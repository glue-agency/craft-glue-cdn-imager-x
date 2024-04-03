if (typeof Craft.GlueCdn === typeof undefined) {
  Craft.GlueCdn = {};
}

// eslint-disable-next-line func-names
(function ($) {
  // eslint-disable-next-line no-undef
  Craft.GlueCdn = Garnish.Base.extend(
    {
      assetModal: null,
      currentElementType: 'Asset',
      $addAssetButton: $('#glue-cdn-utility #addAssetsButton'),
      $assetInput: $('#glue-cdn-utility #imageUrls'),
      $assetList: $('#glue-cdn-utility #imageList'),
      $purgeButton: $('#glue-cdn-utility #purgeButton'),

      init() {
        this.addListener(this.$addAssetButton, 'activate', 'showModal')
        this.addListener(this.$purgeButton, 'activate', 'purgeImages')
      },

      /**
       * Display ElementSelectorModal.
       */
      showModal() {
        this.assetModel = this.createModal("craft\\elements\\Asset", '*');
        if (!this.assetModel) {
          this.assetModel.show();
        }
      },

      /**
       * Create ElementSelectorModal.
       */
      createModal(elementType, elementSources) {
        return Craft.createElementSelectorModal(elementType, {
          criteria: {},
          showSiteMenu: true,
          sources: elementSources,
          multiSelect: true,
          onSelect: $.proxy(this, 'onModalSelect')
        });
      },

      /**
       * Handle selected elements from the ElementSelectorModal.
       */
      onModalSelect(elements) {

        const assetUrls = [];
        this.$assetList.html('');
        // eslint-disable-next-line no-plusplus
        for (let i = 0; i < elements.length; i++) {
          const element = elements[i];
          const listItem = $('<li/>').html(element.label);
          this.$assetList.append(listItem);
          assetUrls.push(element.url);
        }
        this.$assetInput.val(assetUrls.join(','));
        this.$purgeButton.removeClass('hidden');
      },

      purgeImages() {
        const url = Craft.getActionUrl('glue-cdn-imager-x/images/purge');
        const data = {
          asset_urls: this.$assetInput.val()
        }
        Craft.postActionRequest(url, data, $.proxy((response, textStatus) => {
          if (textStatus === 'success') {
            if (response.success) {
              this.$assetList.html('');
              this.$assetInput.val('');
              this.$purgeButton.addClass('hidden');
              Craft.cp.displayNotice(response.message);
            } else {
              Craft.cp.displayError(response.message);
            }
          }
        }, this));
      }
    });
})(jQuery);
