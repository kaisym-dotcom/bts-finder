<?php
/**
 * simple_html_dom.php
 * A simple HTML DOM parser
 * 
 * This is a basic version of the Simple HTML DOM library for demonstration purposes.
 * The full version can be found at: http://sourceforge.net/projects/simplehtmldom/
 */

class simple_html_dom {
    // Load HTML from a string
    public function load($html) {
        $this->dom = new DOMDocument();
        @$this->dom->loadHTML($html);
    }

    // Find elements by CSS selector
    public function find($selector) {
        $xpath = new DOMXPath($this->dom);
        $elements = $xpath->query($this->cssToXPath($selector));
        return $elements;
    }

    // Convert CSS selector to XPath
    private function cssToXPath($selector) {
        $parts = explode(' ', $selector);
        $xpath = '';
        foreach ($parts as $part) {
            if ($part[0] == '#') {
                $xpath .= "//*[@id='" . substr($part, 1) . "']";
            } elseif ($part[0] == '.') {
                $xpath .= "//*[contains(@class, '" . substr($part, 1) . "')]";
            } else {
                $xpath .= '//' . $part;
            }
        }
        return $xpath;
    }
}
?>
