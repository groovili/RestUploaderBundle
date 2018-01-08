<?php
/**
 *
 *  * This file is part of the RestUploaderBundle package.
 *  * (c) groovili
 *  * For the full copyright and license information, please view the LICENSE
 *  * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

namespace Groovili\RestUploaderBundle\Entity;

use function in_array;

/**
 * Class File
 */
final class File
{
    /**
     * Available schemes of file storage
     */
    const SCHEME = [
        'Public' => 0,
        'Private' => 1,
    ];

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $scheme;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $ext;

    /**
     * @var string
     */
    private $path;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var int
     */
    private $size;

    /**
     * File constructor.
     *
     * @param string $name
     * @param string $ext
     * @param string $size
     * @param string $path
     */
    public function __construct(
        int $scheme = null,
        string $name,
        string $ext,
        int $size,
        string $path
    ) {
        $this->setScheme($scheme);
        $this->name = $name;
        $this->ext = $ext;
        $this->size = $size;
        $this->path = $path;
        $this->setCreatedAt(new \DateTime());
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->name;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getScheme(): ?int
    {
        return $this->scheme;
    }

    /**
     * @param int|null|null $scheme
     */
    public function setScheme(?int $scheme = null): void
    {
        if (null === $scheme) {
            $this->scheme = self::SCHEME['Public'];
        } else {
            if (in_array($scheme, self::SCHEME)) {
                $this->scheme = $scheme;
            } else {
                throw new \InvalidArgumentException('Unknown file scheme');
            }
        }
    }

    /**
     * Set name
     *
     * @param string $name
     *
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * @return null|string
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * Set extension
     *
     * @param string $ext
     *
     */
    public function setExt(string $ext): void
    {
        $this->ext = $ext;
    }

    /**
     * @return null|string
     */
    public function getExt(): ?string
    {
        return $this->ext;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @return null|int
     */
    public function getSize(): ?int
    {
        return $this->size;
    }
}