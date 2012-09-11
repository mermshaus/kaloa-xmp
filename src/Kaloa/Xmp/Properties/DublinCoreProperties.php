<?php
/**
 * Kaloa Library (http://www.kaloa.org/)
 *
 * @license http://www.kaloa.org/license.txt MIT License
 */

namespace Kaloa\Xmp\Properties;

use DOMXPath;

use Kaloa\Xmp\Properties\AbstractProperties;

/**
 * Extracts the Dublin Core properties from an XMP document referenced by an
 * XPath instance.
 *
 * The descriptions for all getters are taken from
 * <a href="http://www.exiv2.org/tags-xmp-dc.html">exiv2.org</a>.
 *
 * All getter methods return either an array or a string but never one or the
 * other for the same entity.
 *
 * @todo Currently, internal XMP data structures such as XmpSeq oder XmpBag are
 * represented by PHP arrays. The object model might be extended in later
 * versions.
 */
class DublinCoreProperties extends AbstractProperties
{
    /**
     * See getContributor.
     *
     * @var array
     */
    private $contributor = array();

    /**
     * See getCoverage.
     *
     * @var string
     */
    private $coverage = '';

    /**
     * See getCreator.
     *
     * @var array
     */
    private $creator = array();

    /**
     * See getDate.
     *
     * @var array
     */
    private $date = array();

    /**
     * See getDescription.
     *
     * @var array
     */
    private $description = array();

    /**
     * See getFormat.
     *
     * @var string
     */
    private $format = '';

    /**
     * See getIdentifier.
     *
     * @var string
     */
    private $identifier = '';

    /**
     * See getLanguage.
     *
     * @var array
     */
    private $language = array();

    /**
     * See getPublisher.
     *
     * @var array
     */
    private $publisher = array();

    /**
     * See getRelation.
     *
     * @var array
     */
    private $relation = array();

    /**
     * See getRights.
     *
     * @var array
     */
    private $rights = array();

    /**
     * See getSource.
     *
     * @var string
     */
    private $source = '';

    /**
     * See getSubject.
     *
     * @var array
     */
    private $subject = array();

    /**
     * See getTitle.
     *
     * @var array
     */
    private $title = array();

    /**
     * See getType.
     *
     * @var array
     */
    private $type = array();

    /**
     * Retrieves all properties from the underlying XMP document.
     */
    final protected function init()
    {
        $this->contributor = $this->getArray('contributor');

        foreach ($this->xPath->query('//dc:coverage') as $node) {
            $this->coverage = $node->nodeValue;
        }

        $this->creator = $this->getArray('creator');
        $this->date = $this->getArray('date');
        $this->description = $this->getArray('description');

        // Format

        foreach ($this->xPath->query('//dc:format') as $node) {
            $this->format = $node->nodeValue;
        }

        // Identifier

        foreach ($this->xPath->query('//dc:identifier') as $node) {
            $this->identifier = $node->nodeValue;
        }

        $this->language = $this->getArray('language');
        $this->publisher = $this->getArray('publisher');
        $this->relation = $this->getArray('relation');
        $this->rights = $this->getArray('rights');

        // Source

        foreach ($this->xPath->query('//dc:source//rdf:li') as $node) {
            $this->source = $node->nodeValue;
        }

        // Subject

        $this->subject = $this->getArray('subject');

        // Title

        $this->title = $this->getArray('title');

        if (count($this->title) === 0) {
            foreach ($this->xPath->query('//dc:title') as $node) {
                $this->title[] = $node->nodeValue;
            }
        }

        // Free our reference to the XPath instance.
        $this->xPath = null;
    }

    /**
     * Returns the values of all occurrences of an entity.
     *
     * @param string $entity
     * @return array
     */
    private function getArray($entity)
    {
        $tmp = array();

        foreach ($this->xPath->query('//dc:' . $entity . '//rdf:li') as $node) {
            $tmp[] = $node->nodeValue;
        }

        return $tmp;
    }

    /**
     * Returns contributors to the resource (other than the authors).
     *
     * @return array
     */
    public function getContributor()
    {
        return $this->contributor;
    }

    /**
     * Returns the spatial or temporal topic of the resource, the spatial
     * applicability of the resource, or the jurisdiction under which the
     * resource is relevant.
     *
     * @return string
     */
    public function getCoverage()
    {
        return $this->coverage;
    }

    /**
     * Returns the authors of the resource (listed in order of precedence, if
     * significant).
     *
     * @return array
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Returns date(s) that something interesting happened to the resource.
     *
     * @return array
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Returns a textual description of the content of the resource.
     *
     * Multiple values may be present for different languages.
     *
     * @return array
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the file format used when saving the resource.
     *
     * Tools and applications should set this property to the save format of the
     * data. It may include appropriate qualifiers.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * Returns the unique identifier of the resource.
     *
     * Recommended best practice is to identify the resource by means of a
     * string conforming to a formal identification system.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Returns an unordered array specifying the languages used in the resource.
     *
     * @return array
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Returns an entity responsible for making the resource available.
     *
     * Examples of a Publisher include a person, an organization, or a service.
     * Typically, the name of a Publisher should be used to indicate the entity.
     *
     * @return array
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * Returns relationships to other documents.
     *
     * Recommended best practice is to identify the related resource by means of
     * a string conforming to a formal identification system.
     *
     * @return array
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Returns an informal rights statement, selected by language.
     *
     * Typically, rights information includes a statement about various property
     * rights associated with the resource, including intellectual property
     * rights.
     *
     * @return array
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * Returns the Unique identifier of the work from which this resource was
     * derived.
     *
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Returns an unordered array of descriptive phrases or keywords that
     * specify the topic of the content of the resource.
     *
     * @return array
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Returns the title of the document, or the name given to the resource.
     *
     * Typically, it will be a name by which the resource is formally known.
     *
     * @return array
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Returns a document type; for example, novel, poem, or working paper.
     *
     * @return array
     */
    public function getType()
    {
        return $this->type;
    }
}
