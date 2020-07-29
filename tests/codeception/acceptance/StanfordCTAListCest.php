<?php

/**
 * Codeception tests on CTA List paragraph type.
 */
class StanfordCTAListCest {


  protected function addParagraphField_OLD() {
    \Drupal\field\Entity\FieldStorageConfig::create(array(
      'field_name' => 'page_components',
      'entity_type' => 'node',
      'type' => 'paragraphs',
      'cardinality' => 1,
    ))->save();

    \Drupal\field\Entity\FieldConfig::create([
      'field_name' => 'page_components',
      'entity_type' => 'node',
      'bundle' => 'page',
      'label' => 'paragraphs',
    ])->save();

    entity_get_form_display('node', 'page', 'default')
      ->setComponent('page_components', array(
        'type' => 'paragraphs',
      ))
      ->save();

    entity_get_display('node', 'page', 'default')
      ->setComponent('page_components', array(
        'type' => 'paragraphs',
      ))
      ->save();
  }

  protected function addParagraphField(\AcceptanceTester $I) {
    $I->logInWithRole('administrator');
    $I->amOnPage('/admin/structure/types/manage/page/fields/add-field');
    $I->selectOption('#edit-new-storage-type', 'Paragraph');
    $I->selectOption('#edit-new-storage-type', 'Paragraph');
    $I->seeElement('#edit-label');
    $I->fillField('#edit-label', 'Paragraphs');
    $I->click('Save and Continue');
    $I->seeInCurrentUrl('node.page.field_paragraphs');
    $I->click('Save field settings');
    $I->seeElement('#edit-settings-handler-settings-negate-1');
    $I->click('Save settings');
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
    ], 'paragraph');
    return $paragraph;
  }

  /**
   * Create a node to hold the paragraph.
   */
  protected function createNodeWithParagraph(\AcceptanceTester $I) {
    $this->addParagraphField();

    $paragraph = $this->createParagraph($I);
    $node = $I->createEntity([
      'type' => 'page',
      'title' => 'Test CTA List',
      'su_page_components' => [
        'target_id' => $paragraph->id(),
        'entity' => $paragraph,
        'settings' => json_encode([
          'row' => 0,
          'index' => 0,
          'width' => 12,
          'admin_title' => 'CTA List',
        ]),
      ],
    ]);
    // Clear router and menu cache so that the node urls work.
    $I->runDrush('cache-clear router');
    return $node;
  }

  /**
   * Test the CTA List paragraph in the page.
   */
  public function testCtaList(\AcceptanceTester $I) {
    $node = $this->createNodeWithParagraph($I);
    $I->amOnPage($node->toUrl()->toString());
    $I->canSee('Lorem Ipsum CTA List');
    $I->canSee('Test value for the deck');
    $I->canSeeLink('Link Alpha', 'http://google.com');
    $I->canSeeLink('Link Beta', 'http://google.com');
    $I->canSeeLink('Link Gamma', 'http://google.com');
  }

}
