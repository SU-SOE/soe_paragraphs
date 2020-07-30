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
    // Write our base64 image to the data directory.
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
    $media->setName('Test Image')->setPublished(TRUE)->save();
    return $media;
  }

  /**
   *
   */
  protected function testImage() {
    return '/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDACAhITMkM1EwMFFCLy8vQiccHBwcJyIXFxcXFyIRDAwMDAwMEQwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/2wBDASIzMzQmNCIYGCIUDg4OFBQODg4OFBEMDAwMDBERDAwMDAwMEQwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCADIASwDASIAAhEBAxEB/8QAGgABAAMBAQEAAAAAAAAAAAAAAAQFBgMCAf/EAEkQAQABAgIGAg4FCAoDAAAAAAACAQMEEgUREyIyM0NTFCEjQlJjcXODk6Ozw9MxYnLE8BUkNIKSoqTUNUFRYXSBhLGytOPz9P/EABQBAQAAAAAAAAAAAAAAAAAAAAD/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwDQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAKrExxtbla2ZRpb3cnK8GG151i5c5+1WoDMYnE43DVpS5Om9w5YWZcP+nS8mkfCj7H+WcdN8UPJc+E0QOOHpcpbpte3c6T6P7fFdz5bshYzGxwtKa6Zpy5dqP45aFLGYyFM8rVNn7X3925/BguhGwuJhiYZ4/rw763NGxeP2EqWrcdpdl3ng/jq/SAsnmUqQpWUq6ox3pSVlvE4rPSN21SkZVy57deX53umL+E+aTuX4xrGEaStVhLbXO+t+2t9F4m6CfYxEMRSsrdc0aV2ebh3ty50nnXdl8BdxMLdaWIRnDNXNKfWZbPc/wBJw3R7JpLdZVhGs6ap1jHaR8G5l7tb9YD1Kuqla/3KnRWKu4jPtK5suzybsIcfZG05Fu11S2nw18lVDoPpPQ/ewaAVmOxs8NOEY5cs+PaZvCt+Nt9Y4T0heua64e3ntx6WcZy2nmbdrYfEBdCow+loThKtymSUO963/DeM8U4z0jiaU2lLWq19eM82TrOO17jZgvRDweLji4Zqbso8y34H/iuPmKxM7NaRtwrdlPX9iGTrfxaBNFLPG4u1TPctRyd9lr8q/idl6hZYbExxMM8fsyj4E+rBIEDG46GEpSmrNOXBb+LeQ6Y3GUpnla3P19rl9bd/6gLsU1vSdb1+Fu3SmznTfz0ltoT7rtbXN2XR9UtrlyNuNZyrqjHekD2KWmPxF/XXD26VhTv73fe2wnq9pedsLpCtyexvR2V39yf46LrgWgAAAAAAAAAAAM7pvih5LnwmiZ3TfFDyXPhNEDLzxUIYyVy7SsqW6yt24w1cdr81t86dnx13zyw/LVjwbn7Nv+aRZS7BxtZz5d3Nv/Vvd1uepxfNX0bsJUzUlGtPCzRBQ6IuUrfuRh2oSpK5CP2Ln5v7HFPeOt3MPiKYqFM8e++pudiXdr1W0sdP1i8hcjPXlrSWrdlkrm3nnbW81YZo548VvNvgi4XSFrE7tN2fV3Pg9d73xTrjeRP7E/8AZSaSjCl6Gw1bWtd/ZdZmt9h8np+YvMXGsrM6U+nJP/iCBoXkS85L3eFXCi0NehG3KFa0pLPtN6uXclCzb+CvKV1g+T4a+Sqh0H0nofva+nw18lVDoPpPQ/ewedN8UPJc+Ev7cKW40hH6I0yxUGm+KHkufCaIGZnajXSGXVu5ozy/W2XZvv2lrTX2qs9L+kv84/8AWg0QM7orcxNyFOHVP2V23ate9XWIxVvDU13K/Twx7+al0b+l3PJe9/YecRWlcfSl7l7uXPy8uz/N/Rdm830gJddJSu0rSFm5cjXd/GwsYtx0HWuq5T+ruf73ZHylvfxEMPDNKtNWrcj1nV27Cn0H0nofvYPGHp2TjpSl26W88o+hlDBYb53nGjZqsuwcZWUuXczb3ir/AHX+GxPM82v637dI580cvh5ogoKQpDSOqP0Zs3rLPZFz2l1J01crGEYU7+speq/+hCs3tvjqXKfRKW79iNrY2fWWre0WOmLNblqk6dHXe83c/wDXaBztaWw9qFIRjc1Rpl4bf80gY3Gwv3IXLVJRlDiz5Y8MoXsNybt7x6+wmMt4iFK66Z9XdLcuPP8AK8Yk7WGukc0c0uGGaOcHQAAAAAAAAAAAELFYGGLrSs6ypl18vL33nbV5NAHG9YhfjluUzU/HLucy2rq6Gsa9euf2c0PkbRbgI+Hw1vDUy26atfF28yPf0bZvyrOWaMq8Urcvnbe2sAEDD6Ps4euaNKyl4d3fy+7tezTwBVT0RYnLNvR+pblTJ7W1dWcI0hGkafRGlI/svQD5WmumpEwmChhM2Ssq58ubaZej2nVWrPXJgCFisDDF1pWdZUy6+Xl77ztq8mgCFXAwrf7I1yz+Du7Lg7E6ra8vxyaAIVjAwsXJXY1lmnmzZ8uTuk+yOqt9X1j3iMHaxPMp26cM47s0oBXWdGWLNc1KVnWnDta5/ZdytO2HwcMNKUoVl3Tjz5cvScvZ2rXXJYDhfw9vERy3KZqfvQ81cQYaIsRrrrml9Sctz2Nuy6YvCXL0qXLVytuUaZMveey+XdRa4LGXKZZ3aZfqZvkYX3wI9mlLuPrKHBDweDuNr8n+r27RVprRMJg4YWOqPblXjuS775dpMBWXdE2LlddKSh5qW76u9C/7J6saMs2JUnTNKUeGU5fJ2FtYgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAP/Z';
  }

  /**
   * Create a CTA List paragraph to test.
   */
  protected function createParagraph(\AcceptanceTester $I) {
    $media = $this->setupImage();
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
