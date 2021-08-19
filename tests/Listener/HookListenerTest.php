<?php

declare(strict_types=1);

namespace Alpdesk\AlpdeskBookingTests\Library;

use PHPUnit\Framework\TestCase;
use Alpdesk\AlpdeskFrontendediting\Listener\HooksListener;

class HookListenerTest extends TestCase
{
    public function testIsPatternInString()
    {
        $pattern = 'alpdeskfee_newslist_item_1';

        $this->assertSame(true, HooksListener::isPatternInString($pattern, '<div class="was geht ' . $pattern . '">Ich bin ein String</div>'));
        $this->assertSame(true, HooksListener::isPatternInString($pattern, '<div class="was geht ' . $pattern . '">Ich bin ein String ' . $pattern . '</div>'));
        $this->assertSame(false, HooksListener::isPatternInString($pattern, '<div class="was geht ab">Ich bin ein String</div>'));

        $this->assertSame(false, HooksListener::isPatternInString($pattern, '
<div class="layout_latest arc_2 block first even alpdeskfee_newslist_item_9" itemscope itemtype="http://schema.org/Article">

      <p class="info"><time datetime="2021-09-05T10:02:00+02:00" itemprop="datePublished">05.09.2021 10:02:00</time> von <span itemprop="author">xprojects</span> </p>
  
  
  <h2 itemprop="name"><a href="preview.php/newsreader/teaser-16.html" title="Den Artikel lesen: Teaser 16" itemprop="url"><span itemprop="headline">Teaser 16</span></a></h2>

  <div class="ce_text block" itemprop="description">
    <p>Ich bin der Teaser2</p>  </div>

      <p class="more"><a href="preview.php/newsreader/teaser-16.html" title="Den Artikel lesen: Teaser 16" itemprop="url">Weiterlesen …<span class="invisible"> Teaser 16</span></a></p>
  
</div>
'));

        $this->assertSame(true, HooksListener::isPatternInString($pattern, '
<div class="layout_latest arc_2 block first even ' . $pattern . '" itemscope itemtype="http://schema.org/Article">

      <p class="info"><time datetime="2021-09-05T10:02:00+02:00" itemprop="datePublished">05.09.2021 10:02:00</time> von <span itemprop="author">xprojects</span> </p>
  
  
  <h2 itemprop="name"><a href="preview.php/newsreader/teaser-16.html" title="Den Artikel lesen: Teaser 16" itemprop="url"><span itemprop="headline">Teaser 16</span></a></h2>

  <div class="ce_text block" itemprop="description">
    <p>Ich bin der Teaser2</p>  </div>

      <p class="more"><a href="preview.php/newsreader/teaser-16.html" title="Den Artikel lesen: Teaser 16" itemprop="url">Weiterlesen …<span class="invisible"> Teaser 16</span></a></p>
  
</div>
'));

    }

}
