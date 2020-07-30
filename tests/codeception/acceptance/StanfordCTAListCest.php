<?php

/**
 * Codeception tests on CTA List paragraph type.
 */
class StanfordCTAListCest {

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
   * Create a CTA List paragraph to test.
   */
  protected function createParagraph(\AcceptanceTester $I) {
    $paragraph = $I->createEntity([
      'type' => 'stanford_cta_list',
      'stanford_cta_list_header' => [
        'value' => 'Lorem Ipsum CTA List',
      ],
      'stanford_cta_list_deck' => [
        'value' => 'Test value for the deck',
      ],
      'stanford_cta_list_links' => [
        [
          'uri' => 'http://google.com',
          'title' => 'Link Alpha',
        ],
        [
          'uri' => 'http://google.com',
          'title' => 'Link Beta',
        ],
        [
          'uri' => 'http://google.com',
          'title' => 'Link Gamma',
        ],
      ],
    ], 'paragraph', TRUE);
    return $paragraph;
  }

  /**
   * Create a node to hold the paragraph.
   */
  protected function createNodeWithParagraph(\AcceptanceTester $I) {
    $paragraph = $this->createParagraph($I);
    $node = $I->createEntity([
      'type' => 'page',
      'title' => 'Test CTA List',
    ]);
    $node->field_paragraphs->appendItem($paragraph);
    $node->save();

    $I->runDrush('cache-rebuild');
    return $node;
  }

  /**
   * Test the CTA List paragraph in the page.
   */
  public function testCtaList(\AcceptanceTester $I) {
    $I->runDrush('pm-enable soe_paragraph_cta_list');
    $this->setupContentType($I);
    $node = $this->createNodeWithParagraph($I);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee('Lorem Ipsum CTA List');
    $I->canSee('Test value for the deck');
    $I->canSeeLink('Link Alpha', 'http://google.com');
    $I->canSeeLink('Link Beta', 'http://google.com');
    $I->canSeeLink('Link Gamma', 'http://google.com');
    $this->revertContentType($I);
  }

}
