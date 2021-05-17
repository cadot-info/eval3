<?php

namespace App\Controller;

use DateTime;
use App\Entity\Crypto;
use App\Entity\Resultat;
use App\Form\CryptoType;
use Symfony\UX\Chartjs\Model\Chart;
use App\Repository\CryptoRepository;
use App\Repository\ResultatRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("")
 */
class CryptoController extends AbstractController
{
    protected $cryptoRepository;

    public function __construct(CryptoRepository $cryptoRepository)
    {
        $this->cryptoRepository = $cryptoRepository;
    }

    /**
     * @Route("/", name="crypto_index", methods={"GET"})
     */
    public function index(ChartBuilderInterface $chartBuilder, CryptoRepository $cryptoRepository, ResultatRepository $resultats): Response
    {
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $dat = [];
        $val = [];
        foreach ($resultats->findAll() as $key => $value) {
            $dat[] = $value->getDate()->format('d-m-Y');
            $val[] = $value->getValeur();
        }

        $chart->setData([
            'labels' => $dat,
            'datasets' => [
                [
                    'label' => 'Vos gains',
                    'backgroundColor' => 'rgb(0, 0, 0)',
                    'borderColor' => 'rgb(31,195,108)',
                    'data' => $val,
                ],
            ],
        ]);

        $chart->setOptions([/* ... */]);
        try {
            $total = $this->get_total();
        } catch (\Throwable $th) {
            throw $th;
        }


        return $this->render('crypto/index.html.twig', [
            'cryptos' => $cryptoRepository->findAll(),
            'total' => $total,
            'chart' => $chart,
        ]);
    }

    /**
     * @Route("/new", name="crypto_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $crypto = new Crypto();
        $form = $this->createForm(CryptoType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($crypto);
            $entityManager->flush();

            return $this->redirectToRoute('crypto_index');
        }

        return $this->render('crypto/new.html.twig', [
            'crypto' => $crypto,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/save_crypto", name="crypto_save", methods={"GET"})
     * function for call by cron, nodejs... for save the total of symbol for current day
     */
    public function save_crypto(): Response
    {
        try {
            $total = $this->get_total()['total'];
            $resultat = new Resultat();
            $resultat->setValeur($total);
            $resultat->setDate(new DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($resultat);
            $entityManager->flush();
            return new JsonResponse(
                'Save ended',
                200
            );
        } catch (\Throwable $th) {
            return new JsonResponse('Error:' . $th->getMessage(), 500);
        }
    }


    /**
     * @Route("/{id}", name="crypto_show", methods={"GET"})
     */
    public function show(Crypto $crypto): Response
    {
        return $this->render('crypto/show.html.twig', [
            'crypto' => $crypto,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="crypto_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Crypto $crypto): Response
    {
        $form = $this->createForm(CryptoType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('crypto_index');
        }

        return $this->render('crypto/new.html.twig', [
            'crypto' => $crypto,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/delete", name="crypto_delete", methods={"GET","POST"})
     */
    public function delete(Request $request, Crypto $crypto): Response
    {
        $form = $this->createForm(CryptoType::class, $crypto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->isCsrfTokenValid('delete' . $crypto->getId(), $request->request->get('_token'))) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($crypto);
                $entityManager->flush();
            }

            return $this->redirectToRoute('crypto_index');
        }

        return $this->render('crypto/delete.html.twig', [
            'crypto' => $crypto,
            'form' => $form->createView(),
        ]);
    }


    /**
     * Method get_total
     *
     * @return integer total of value in wallet with actual value
     */
    public function get_total()
    {
        //contains symbols commas
        $symbols = [];
        //loop for read symbols of bd
        foreach ($this->cryptoRepository->findAll() as $symbol) {
            $symbols[] = ($symbol->getsymbol());
        }
        $url = 'https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest';
        $parameters = [
            'convert' => 'EUR',
            'symbol' => implode(',', $symbols)
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: ' . $_ENV['COINMARKETCAP']
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL


        $curl = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers 
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        $resultats = json_decode($response, true);
        curl_close($curl); // Close request
        //get all for amount of day
        $total = 0;
        $data = $resultats['data'];
        foreach ($this->cryptoRepository->findAll() as $symbol) {
            $valeur_actuelle = $data[$symbol->getSymbol()]['quote']['EUR']['price'];
            $total +=   $symbol->getQuantite() * ($valeur_actuelle - $symbol->getPrixAchat());
        }
        $resultats['total'] = $total;
        return $resultats;
    }
}
