<?php

namespace App\Domain\Template\Schema;

/**
 * Contains necessary attributes, such as templates selectors which we are utilize
 * to parse HTML template into separated sections and components.
 *
 * Class TemplateSchema
 *
 * @author Andrzej Tracz <andrzej.tracz7@gmail.com>
 */
final class TemplateSchema
{
    /**
     * Selector of template section.
     */
    const SELECTOR_PREHEADER = '.preheader'; //'*[data-section]';

    /**
     * Selector of template section.
     */
    const SELECTOR_SECTION = 'body > table'; //'*[data-section]';

    /**
     * Selector of section's editable components.
     */
    const SELECTOR_EDITABLE = '*[data-editable]';

    /**
     * Selector of sections's repeatable components.
     */
    const SELECTOR_REPEATABLE = '*[data-repeatable]';

    /**
     * Selector of section's draggable components.
     */
    const SELECTOR_DRAGGABLE = '*[data-draggable]';

    /**
     * Selector of sections's dropable components.
     */
    const SELECTOR_DROPABLE = '*[data-dropable]';

    /**
     * Selector of section's components contained background image and/or color.
     */
    const SELECTOR_BACKGROUND = '*[data-background]';

    /**
     * Attribute which determine associated thumbnail image.
     */
    const ATTRIBUTE_SECTION_THUMBNAIL = 'data-thumbnail';

    /**
     * Attribute which determine section name, should be unique alongside template.
     */
    const ATTRIBUTE_SECTION_NAME = 'data-section';
}
