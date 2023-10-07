<?php 

class MyDomDoc extends DOMDocument
{
    public function load($filename, $resolveIncludes = true, $options = null)
    {
        chdir(dirname($filename));

        parent::load($filename, $options);

        if ($resolveIncludes) {
            $this->resolveIncludes();
        }
    }

    public function resolveIncludes()
    {
        $this->resolveNodeIncludes($this);
    }

    private function resolveNodeIncludes(DOMNode $node)
    {
        if ($this->isIncludeNode($node)) {
            $included = new static();
            $included->load($node->attributes->getNamedItem('schemaLocation')->textContent);
            $this->replaceIncludedElements($included, $node);
        }
        elseif ($node->childNodes) {
            foreach ($node->childNodes as $node) {
                $this->resolveNodeIncludes($node);
            }
        }
    }

    private function replaceIncludedElements(DOMDocument $included, DOMNode $replace)
    {
        foreach ($included->firstChild->childNodes as $childNode) {
            $replace->parentNode->insertBefore($this->importNode($childNode, true), $replace);
        }

        $replace->parentNode->removeChild($replace);
    }

    private function isIncludeNode(DOMNode $node)
    {
        return $node->localName == 'include' && $node->namespaceURI == 'http://www.w3.org/2001/XMLSchema';
    }
}