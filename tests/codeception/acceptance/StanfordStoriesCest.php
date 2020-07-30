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
   *
   */
  protected function setupContentType(\AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/types/manage/page/fields/add-field');
    $I->selectOption('#edit-new-storage-type', 'Paragraph');
    $I->selectOption('#edit-new-storage-type', 'Paragraph');
    $I->seeElement('#edit-label');
    $I->fillField('#edit-label', 'Paragraphs');
    $I->click('//input[@class="button button--primary js-form-submit form-submit"]');
    $I->seeElement('#edit-field-name');
    $I->fillField('#edit-field-name', 'paragraphs');
    $I->click('//input[@class="button button--primary js-form-submit form-submit"]');
    $I->seeInCurrentUrl('node.page.field_paragraphs');
    $I->click('//input[@id="edit-submit"]');
    $I->seeElement('#edit-settings-handler-settings-negate-1');
    $I->click('#edit-settings-handler-settings-negate-1');
    $I->click('//input[@id="edit-submit"]');
    drupal_flush_all_caches();
  }

  /**
   *
   */
  protected function revertContentType(\AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/types/manage/page/fields/node.page.field_paragraphs/delete');
    $I->click('//input[@id="edit-submit"]');
    drupal_flush_all_caches();
  }

  /**
   *
   */
  protected function setupImage() {
    $file = fopen(codecept_data_dir() . '/test_image1.jpg', 'wb');
    fwrite($file, base64_decode($this->testImage()));
    fclose($file);
    $file_data = file_get_contents(codecept_data_dir() . '/test_image1.jpg');
    $uploaded = file_save_data($file_data, 'public://test_image1.jpg', FILE_EXISTS_REPLACE);
    $media = Media::create([
      'bundle' => 'image',
      'uid' => \Drupal::currentUser()->id(),
      'field_media_file' => [
        'target_id' => $uploaded->id(),
      ],
    ]);
    $media->setName('Test Image 290')->setPublished(TRUE)->save();
    return $media;
  }

  /**
   *
   */
  protected function testImage() {
    return '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';
  }

  /**
   * Create a Stories paragraph to test.
   */
  protected function createParagraph(\AcceptanceTester $I) {
    $media = $this->setupImage();
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
