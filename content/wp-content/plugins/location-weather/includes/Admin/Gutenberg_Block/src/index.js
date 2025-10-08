import icons from "./shortcode/blockIcon";
import DynamicShortcodeInput from "./shortcode/dynamicShortcode";
import { escapeAttribute, escapeHTML } from "@wordpress/escape-html";
import { Fragment, createElement } from "@wordpress/element";
import { __ } from "@wordpress/i18n";
import { registerBlockType } from "@wordpress/blocks";
import { PanelBody, PanelRow } from "@wordpress/components";
import { InspectorControls } from '@wordpress/block-editor';
const ServerSideRender =  wp.serverSideRender;
const el = createElement;

/**
 * Register: location weather Gutenberg Block.
 */
registerBlockType("sp-location-weather-pro/shortcode", {
  title: escapeHTML(__("Location Weather", "location-weather")),
  description: escapeHTML(__(
    "Use Location Weather to insert a shortcode in your page.",
    "location-weather"
  )),
  icon: icons.splwp_icon,
  category: "common",
  supports: {
    html: true,
  },
  edit: (props) => {
    const { attributes, setAttributes } = props;
    var shortCodeList = sp_location_weather.shortCodeList;
    let scriptLoad = (shortcodeId) => {
      let spwpcpBlockLoaded = false;
      let spwpcpBlockLoadedInterval = setInterval(function () {
        let uniqId = jQuery("#splw-location-weather-" + shortcodeId).parents().attr('id');
        if (document.getElementById(uniqId)) {
          //Actual functions goes here
          jQuery.getScript(sp_location_weather.loadScript);
          spwpcpBlockLoaded = true;
          uniqId = '';
        }
        if (spwpcpBlockLoaded) {
          clearInterval(spwpcpBlockLoadedInterval);
        }
        if (0 == shortcodeId) {
          clearInterval(spwpcpBlockLoadedInterval);
        }
      }, 10);
    }
    let updateShortcode = (updateShortcode) => {
      setAttributes({ shortcode: escapeAttribute(updateShortcode.target.value) });
    }

    let shortcodeUpdate = (e) => {
      updateShortcode(e);
      let shortcodeId = escapeAttribute(e.target.value);
      scriptLoad(shortcodeId);
    }

    document.addEventListener('readystatechange', event => {
      if (event.target.readyState === "complete") {
        let shortcodeId = escapeAttribute(attributes.shortcode);
        scriptLoad(shortcodeId);
      }
    });

    if (attributes.preview) {
      return el(
        "div",
        { className: "spwpcp_shortcode_block_preview_image" },
        el("img", {
          src: escapeAttribute(
            sp_location_weather.url +
            "/includes/Admin/Gutenberg_Block/assets/lw-block-preview.svg"
          ),
        })
      );
    }

    if (shortCodeList.length === 0) {
      return (
        <Fragment>
          {el(
            "div",
            {
              className:
                "components-placeholder components-placeholder is-large",
            },
            el(
              "div",
              { className: "components-placeholder__label" },
              el("img", {
                className: "block-editor-block-icon",
                src: escapeAttribute(
                  sp_location_weather.url +
                  "admin/GutenbergBlock/assets/wp-carousel-icon.svg"
                ),
              }),
              escapeHTML(__("Location Weather", "location-weather"))
            ),
            el(
              "div",
              { className: "components-placeholder__instructions" },
              escapeHTML(__("No shortcode found. ", "location-weather")),
              el(
                "a",
                { href: escapeAttribute(sp_location_weather.link) },
                escapeHTML(__("Create a shortcode now!", "location-weather"))
              )
            )
          )}
        </Fragment>
      );
    }

    if (!attributes.shortcode || attributes.shortcode == 0) {
      return (
        <Fragment>
          <InspectorControls>
            <PanelBody title="Select a shortcode">
              <PanelRow>
                <DynamicShortcodeInput
                  attributes={attributes}
                  shortCodeList={shortCodeList}
                  shortcodeUpdate={shortcodeUpdate}
                />
              </PanelRow>
            </PanelBody>
          </InspectorControls>
          {el(
            "div",
            {
              className:
                "components-placeholder components-placeholder is-large",
            },
            el(
              "div",
              { className: "components-placeholder__label" },
              el("img", {
                className: "block-editor-block-icon",
                src: escapeAttribute(
                  sp_location_weather.url +
                  "/includes/Admin/Gutenberg_Block/assets/lw-icon.svg"
                ),
              }),
              escapeHTML(__("Location Weather", "location-weather"))
            ),
            el(
              "div",
              { className: "components-placeholder__instructions" },
              escapeHTML(__("Select a shortcode", "location-weather"))
            ),
            <DynamicShortcodeInput
              attributes={attributes}
              shortCodeList={shortCodeList}
              shortcodeUpdate={shortcodeUpdate}
            />
          )}
        </Fragment>
      );
    }

    return (
      <Fragment>
        <InspectorControls>
          <PanelBody title="Select a shortcode">
            <PanelRow>
              <DynamicShortcodeInput
                attributes={attributes}
                shortCodeList={shortCodeList}
                shortcodeUpdate={shortcodeUpdate}
              />
            </PanelRow>
          </PanelBody>
        </InspectorControls>
        <ServerSideRender block="sp-location-weather-pro/shortcode" attributes={attributes} />
      </Fragment>
    );
  },
  save() {
    // Rendering in PHP
    return null;
  },
});
