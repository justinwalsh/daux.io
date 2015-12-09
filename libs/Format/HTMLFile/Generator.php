<?php namespace Todaymade\Daux\Format\HTMLFile;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Todaymade\Daux\Console\RunAction;
use Todaymade\Daux\ContentTypes\Markdown\ContentType;
use Todaymade\Daux\Daux;

class Generator implements \Todaymade\Daux\Format\Base\Generator
{
    use RunAction;

    /** @var Daux */
    protected $daux;

    /**
     * @param Daux $daux
     */
    public function __construct(Daux $daux)
    {
        $this->daux = $daux;
    }

    /**
     * @return array
     */
    public function getContentTypes()
    {
        return [
            'markdown' => new ContentType($this->daux->getParams())
        ];
    }

    protected function initPDF()
    {
        // create new PDF document
        $pdf = new Book(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $params = $this->daux->getParams();

        // set document information
        $pdf->SetCreator(PDF_CREATOR);


        // set default header data
        $pdf->SetHeaderData('', 0, $params['title'], $params['tagline']);

        // set header and footer fonts
        $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set font
        $pdf->SetFont('helvetica', '', 10);

        return $pdf;
    }


    /**
     * {@inheritdoc}
     */
    public function generateAll(InputInterface $input, OutputInterface $output, $width)
    {
        $params = $this->daux->getParams();

        $data = ['author' => $params['author'], 'title' => $params['title'], 'subject' => $params['tagline']];

        $book = new Book($this->daux->tree, $data);

        $current = $this->daux->tree->getIndexPage();
        while ($current) {
            $this->runAction(
                "Generating " . $current->getTitle(),
                $output,
                $width,
                function () use ($book, $current, $params) {
                    $contentType = $this->daux->getContentTypeHandler()->getType($current);
                    $content = ContentPage::fromFile($current, $params, $contentType)->getContent();
                    $book->addPage($current, $content);
                }
            );

            $current = $current->getNext();
        }

        $content = $book->generate();
        file_put_contents($input->getOption('destination') . '/file.html', $content);
    }
}
