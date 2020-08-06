<?php

/**
 * Codeception tests on Image CTA paragraph type.
 */

use Drupal\media\Entity\Media;

/**
 *
 */
class StanfordImageCTACest {

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
   * Create an Image CTA paragraph to test.
   */
  protected function createParagraph(\AcceptanceTester $I) {
    $media = $this->setupImage($I);
    $paragraph = $I->createEntity([
      'type' => 'stanford_image_cta',
      'stanford_image_cta_image' => [
        'target_id' => $media->id(),
      ],
      'stanford_image_cta_link' => [
        'uri' => 'http://google.com',
        'title' => 'Link Alpha',
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
      'title' => 'Test Image CTA',
    ]);
    $node->field_paragraphs->appendItem($paragraph);
    $node->save();
    $I->runDrush('cache-rebuild');
    return $node;
  }

  /**
   * Test the Image CTA paragraph in the page.
   */
  public function testImageCta(\AcceptanceTester $I) {
    $I->runDrush('pm-enable soe_paragraph_image_cta');
    $this->setupContentType($I);
    $node = $this->createNodeWithParagraph($I);
    $I->amOnPage($node->toUrl()->toString());
    $I->seeElement("//div[contains(@class, 'su-image-cta-paragraph__image')]");
    $I->seeLink('Link Alpha', 'http://google.com');
    $this->revertContentType($I);
  }

}
