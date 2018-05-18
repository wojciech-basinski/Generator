<?php
namespace App\Controller;

use App\Form\GeneratingType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class GeneratingController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request->getCurrentRequest();
    }

    /**
     * @Route("/", name="generate_index")
     */
    public function generateIndex()
    {
        if ($this->request->request->get('numberOfCodes') && $this->request->request->get('codeLength')) {
            return $this->redirectToRoute('generate_generating');
        }

        $form = $this->createForm(GeneratingType::class, null, ['action' => $this->generateUrl('generate_generating'),]);

        return $this->render('form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/generate", name="generate_generating")
     */
    public function generating()
    {
        if (!$this->request->request->get('generating')['numberOfCodes'] || !$this->request->request->get('generating')['codeLength']) {
            return $this->redirectToRoute('generate_index');
        }
        $numberOfCodes = (int)$this->request->request->get('generating')['numberOfCodes'];
        $codeLength = (int)$this->request->request->get('generating')['codeLength'];

        $generatingService = $this->get('generator');
        if ($generatingService->checkValue($numberOfCodes)) {
            $this->addFlash('error', 'Wrong value for number of codes');
        }
        if ($generatingService->checkValue($codeLength)) {
            $this->addFlash('error', 'Wrong value for code length');
        }
        if ($generatingService->errors) {
            return $this->redirectToRoute('generate_index');
        }

        $generatingService->generate($numberOfCodes, $codeLength);

        if ($generatingService->errors) {
            return $this->redirectToRoute('generate_index');
        }

        $link = $generatingService->getDownloadLink();

        return $this->render('codes.html.twig', [
            'DOWNLOADLINK' => $link
        ]);
    }
}