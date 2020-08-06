<?php

/**
 * Codeception tests on Stories paragraph type.
 */

use Drupal\media\Entity\Media;

/**
 *
 */
class StanfordStoriesCest {

  /**
   * Create a paragraphs field on the basic page content type.
   */
  protected function setupContentType(\AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/types/manage/page/fields/add-field');
    $I->selectOption('Add a new field', 'Paragraph');
    $I->fillField('Label', 'Paragraphs');
    $I->fillField('Machine-readable name', 'paragraphs');
    $I->click('Save and continue');
    $I->click('Save field settings');
    $I->selectOption('Exclude the selected below', 1);
    $I->click('Save settings');
    $I->see('Saved Paragraphs configuration');
    drupal_flush_all_caches();
  }

  /**
   * Remove the paragraphs field from the basic page content type.
   */
  protected function revertContentType(\AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/types/manage/page/fields/node.page.field_paragraphs/delete');
    $I->click('Delete');
    drupal_flush_all_caches();
  }

  /**
   * Set up a test image in the media library.
   */
  protected function setupImage(\AcceptanceTester $I) {
    $file_data = file_get_contents(dirname(__FILE__) . '/test_image.jpg');
    $uploaded = file_save_data($file_data, 'public://test_image.jpg', FILE_EXISTS_REPLACE);
    $media = $I->createEntity([
      'bundle' => 'image',
      'uid' => \Drupal::currentUser()->id(),
      'field_media_file' => [
        'target_id' => $uploaded->id(),
      ],
    ], 'media');
    $media->setName('Test Image')->setPublished(TRUE)->save();
    return $media;
  }

  /**
   * Create a Stories paragraph to test.
   */
  protected function createParagraph(\AcceptanceTester $I) {
    $media = $this->setupImage($I);
    $paragraph = $I->createEntity([
      'type' => 'stanford_stories',
      'stanford_stories_cta_link' => [
        'uri' => 'http://google.com',
        'title' => 'Link Alpha',
      ],
      'stanford_stories_node_link' => [
        'uri' => 'http://yahoo.com',
        'title' => 'Link Beta',
      ],
      'stanford_stories_name' => [
        'value' => 'Test value for the name',
      ],
      'stanford_stories_photo' => [
        'target_id' => $media->id(),
      ],
      'stanford_stories_border' => [
        'value' => '1',
      ],
      'stanford_stories_quote' => [
        'value' => 'Test value for the quote',
      ],
      'stanford_stories_title' => [
        'value' => 'Test value for the title',
      ],
    ], 'paragraph');
    return $paragraph;
  }

  /**
   * Create a node to hold the paragraph.
   */
  protected function createNodeWithParagraph(\AcceptanceTester $I) {
    $paragraph = $this->createParagraph($I);
    $node = $I->createEntity([
      'type' => 'page',
      'title' => 'Test Stories Paragraph',
    ]);
    $node->field_paragraphs->appendItem($paragraph);
    $node->save();
    $I->runDrush('cache-rebuild');
    return $node;
  }

  /**
   * Test the CTA List paragraph in the page.
   */
  public function testStories(\AcceptanceTester $I) {
    $I->runDrush('pm-enable soe_paragraph_stories');
    $this->setupContentType($I);
    $node = $this->createNodeWithParagraph($I);
    $I->amOnPage($node->toUrl()->toString());
    $I->seeElement('.border-color-1');
    $I->seeElement("//div[contains(@class, 'su-stories-paragraph__photo')]");
    $I->seeLink('Link Alpha', 'http://google.com');
    $I->seeLink('Link Beta', 'http://yahoo.com');
    $I->see('Test value for the name', '.su-stories-paragraph__name');
    $I->see('Test value for the quote', '.su-stories-paragraph__quote');
    $I->see('Test value for the title', '.su-stories-paragraph__title');
    $this->revertContentType($I);
  }

}
