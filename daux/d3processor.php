<?php

namespace Todaymade\Daux\Extension;

use League\CommonMark\Block\Element\BlockQuote;
use League\CommonMark\Block\Element\Document;
use League\CommonMark\Block\Element\Paragraph;
use League\CommonMark\Environment;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\InlineParserContext;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Parser\AbstractInlineParser;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
USE League\CommonMark\Block\Element\AbstractBlock;
USE League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Code;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Util\Configuration;
use Todaymade\Daux\Daux;
use Symfony\Component\Console\Output\NullOutput;

class d3Parser extends AbstractInlineParser
{
    public function getCharacters() {
        return ['v', 'V'];
    }

    public function parse(InlineParserContext $inlineContext)
    {
        $cursor = $inlineContext->getCursor();

        if ($cursor->match('/D3/')) {
            $inlineContext->getContainer()->appendChild(new Code('d3logo'));
            return true;
        }

        return false;
    }
}

class d3BlockQuoteRenderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof BlockQuote)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $attrs = [];
        foreach ($block->getData('attributes', []) as $key => $value) {
            $attrs[$key] = $htmlRenderer->escape($value, true);
        }

        $filling = $htmlRenderer->renderBlocks($block->children());

        if ($filling === '') {
            return new HtmlElement('blockquote', $attrs, $htmlRenderer->getOption('inner_separator', "\n"));
        }

        if (stristr($filling, '[!!]')) {
            $attrs['class'] = isset($attrs['class']) ? $attrs['class'].' alert alert-danger' : 'alert alert-danger';
            $filling = "<i class='fas fa-exclamation-circle'></i> ".trim(str_replace('[!!]', '', $filling));
        }

        if (stristr($filling, '[!]')) {
            $attrs['class'] = isset($attrs['class']) ? $attrs['class'].' alert alert-warning' : 'alert alert-warning';
            $filling = "<i class='fas fa-exclamation-triangle'></i> ".trim(str_replace('[!]', '', $filling));
        }

        if (stristr($filling, '[i]')) {
            $attrs['class'] = isset($attrs['class']) ? $attrs['class'].' alert alert-info' : 'alert alert-info';
            $filling = "<i class='fas fa-info-circle'></i> ".trim(str_replace('[i]', '', $filling));
        }

        return new HtmlElement(
            'blockquote',
            $attrs,
            $htmlRenderer->getOption('inner_separator', "\n") . $filling . $htmlRenderer->getOption('inner_separator', "\n")
        );
    }
}

class d3ParagraphRenderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof Paragraph)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $pattern1 = '/\[\s*?(.{3,})\s*?\]/Uis';
        $replace1 = '<span class="navi_element">\\1</span>';

        if ($inTightList) {
            $content = $htmlRenderer->renderInlines($block->children());
            $content = preg_replace($pattern1, $replace1, $content);

            return $content;
        } else {
            $attrs = [];
            foreach ($block->getData('attributes', []) as $key => $value) {
                $attrs[$key] = $htmlRenderer->escape($value, true);
            }

            $content = $htmlRenderer->renderInlines($block->children());
            $content = preg_replace($pattern1, $replace1, $content);

            return new HtmlElement('p', $attrs, $content);
        }
    }
}

class d3DocumentRenderer implements BlockRendererInterface
{
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof Document)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $wholeDoc = $htmlRenderer->renderBlocks($block->children());

        $output = new NullOutput();
        $daux = new Daux(Daux::LIVE_MODE, $output);
        $daux->initializeConfiguration();

        $variables = $daux->getParams()['variables'];
        if (isset($variables) && is_array($variables) && count($variables)) {
            foreach ($variables as $varname => $varvalue) {
                $pattern = '/{\$'.$varname.'}/mU';
                $wholeDoc = preg_replace($pattern, $varvalue, $wholeDoc);
            }
        }

        return $wholeDoc === '' ? '' : $wholeDoc . "\n";
    }
}

class d3TextRenderer implements InlineRendererInterface
{
    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @param AbstractInline $inline
     * @param ElementRendererInterface $htmlRenderer
     * @return HtmlElement|mixed|string
     * @throws \Todaymade\Daux\Exception
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (!($inline instanceof Text)) {
            throw new \InvalidArgumentException('Incompatible inline type: ' . get_class($inline));
        }

        $content = $htmlRenderer->escape($inline->getContent());

        $search = array(
            'D3', 'D³', 'D&sup3;'
        );
        $replace = "<i class='fab fa-d3 d3fa-color-blue'></i>";
        $content = str_replace($search, $replace, $content);

        return $content;
    }
}

class d3processor extends \Todaymade\Daux\Processor
{
    public function extendCommonMarkEnvironment(Environment $environment)
    {
        // format important and info code blocks
        $environment->addBlockRenderer('League\CommonMark\Block\Element\BlockQuote', new d3BlockQuoteRenderer());
        $environment->addBlockRenderer('League\CommonMark\Block\Element\Paragraph',  new d3ParagraphRenderer());
        $environment->addBlockRenderer('League\CommonMark\Block\Element\Document',  new d3DocumentRenderer());
        $environment->addInlineRenderer('League\CommonMark\Inline\Element\Text', new d3TextRenderer());
    }
}
