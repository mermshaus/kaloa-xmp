<?php

declare(strict_types=1);

/*
 * This file is part of the kaloa/xmp package.
 *
 * For full copyright and license information, please view the LICENSE file
 * that was distributed with this source code.
 */

namespace Kaloa\Xmp\Properties;

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
     */
    private array $contributor = [];

    /**
     * See getCoverage.
     */
    private string $coverage = '';

    /**
     * See getCreator.
     */
    private array $creator = [];

    /**
     * See getDate.
     */
    private array $date = [];

    /**
     * See getDescription.
     */
    private array $description = [];

    /**
     * See getFormat.
     */
    private string $format = '';

    /**
     * See getIdentifier.
     */
    private string $identifier = '';

    /**
     * See getLanguage.
     */
    private array $language = [];

    /**
     * See getPublisher.
     */
    private array $publisher = [];

    /**
     * See getRelation.
     */
    private array $relation = [];

    /**
     * See getRights.
     */
    private array $rights = [];

    /**
     * See getSource.
     */
    private string $source = '';

    /**
     * See getSubject.
     */
    private array $subject = [];

    /**
     * See getTitle.
     */
    private array $title = [];

    /**
     * See getType.
     */
    private array $type = [];

    /**
     * Retrieves all properties from the underlying XMP document.
     */
    final protected function init(): void
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
    }

    /**
     * Returns the values of all occurrences of an entity.
     */
    private function getArray(string $entity): array
    {
        $tmp = [];

        foreach ($this->xPath->query('//dc:' . $entity . '//rdf:li') as $node) {
            $tmp[] = $node->nodeValue;
        }

        return $tmp;
    }

    /**
     * Returns contributors to the resource (other than the authors).
     */
    public function getContributor(): array
    {
        return $this->contributor;
    }

    /**
     * Returns the spatial or temporal topic of the resource, the spatial
     * applicability of the resource, or the jurisdiction under which the
     * resource is relevant.
     */
    public function getCoverage(): string
    {
        return $this->coverage;
    }

    /**
     * Returns the authors of the resource (listed in order of precedence, if
     * significant).
     */
    public function getCreator(): array
    {
        return $this->creator;
    }

    /**
     * Returns date(s) that something interesting happened to the resource.
     */
    public function getDate(): array
    {
        return $this->date;
    }

    /**
     * Returns a textual description of the content of the resource.
     *
     * Multiple values may be present for different languages.
     */
    public function getDescription(): array
    {
        return $this->description;
    }

    /**
     * Returns the file format used when saving the resource.
     *
     * Tools and applications should set this property to the save format of the
     * data. It may include appropriate qualifiers.
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Returns the unique identifier of the resource.
     *
     * Recommended best practice is to identify the resource by means of a
     * string conforming to a formal identification system.
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * Returns an unordered array specifying the languages used in the resource.
     */
    public function getLanguage(): array
    {
        return $this->language;
    }

    /**
     * Returns an entity responsible for making the resource available.
     *
     * Examples of a Publisher include a person, an organization, or a service.
     * Typically, the name of a Publisher should be used to indicate the entity.
     */
    public function getPublisher(): array
    {
        return $this->publisher;
    }

    /**
     * Returns relationships to other documents.
     *
     * Recommended best practice is to identify the related resource by means of
     * a string conforming to a formal identification system.
     */
    public function getRelation(): array
    {
        return $this->relation;
    }

    /**
     * Returns an informal rights statement, selected by language.
     *
     * Typically, rights information includes a statement about various property
     * rights associated with the resource, including intellectual property
     * rights.
     */
    public function getRights(): array
    {
        return $this->rights;
    }

    /**
     * Returns the Unique identifier of the work from which this resource was
     * derived.
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Returns an unordered array of descriptive phrases or keywords that
     * specify the topic of the content of the resource.
     */
    public function getSubject(): array
    {
        return $this->subject;
    }

    /**
     * Returns the title of the document, or the name given to the resource.
     *
     * Typically, it will be a name by which the resource is formally known.
     */
    public function getTitle(): array
    {
        return $this->title;
    }

    /**
     * Returns a document type; for example, novel, poem, or working paper.
     */
    public function getType(): array
    {
        return $this->type;
    }
}
